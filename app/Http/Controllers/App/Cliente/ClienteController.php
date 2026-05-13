<?php

namespace App\Http\Controllers\App\Cliente;

use App\Filters\App\Cliente\ClienteFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\ClienteRequest as Request;
use App\Imports\App\ClienteImport;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Cliente\Cliente;
use App\Models\App\SuperPartner\SuperPartner;
use App\Services\App\Cliente\ClienteService;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ClienteController extends Controller
{
    protected $clienteAccessMailService;

    /**
     * ClienteController constructor.
     * @param ClienteService $service
     * @param ClienteFilter $filter
     */
    public function __construct(ClienteService $service, ClienteFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
        $this->clienteAccessMailService = app('App\Services\App\Cliente\ClienteAccessMailService');
    }

    /**
     * Display a listing of clientes.
     * 
     * If authenticated user is a beneficiario, only show their clients.
     * If authenticated user is a super_partner, only show clients of their partners.
     * 
     * @return mixed
     */
    public function index()
    {
        $query = $this->service->filters($this->filter)->latest();
        $superPartner = $this->resolveScopedSuperPartner();
        
        // Filter by beneficiario_id if user is a beneficiario
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            
            if ($beneficiario) {
                // Show clients whose primary beneficiario is this partner
                // OR clients associated through the pivot cliente_beneficiario table
                $query = $query->where(function ($q) use ($beneficiario) {
                    $q->where('beneficiario_id', $beneficiario->id)
                      ->orWhereHas('partners', function ($partnerQuery) use ($beneficiario) {
                          $partnerQuery->where('beneficiario_id', $beneficiario->id);
                      });
                });
            }
        } elseif ($superPartner) {
            $partnerIds = $superPartner->beneficiarios()->pluck('id');

            // El super partner y sus usuarios solo ven clientes de su propia red.
            $query = $query->where(function ($q) use ($partnerIds, $superPartner) {
                $q->whereIn('beneficiario_id', $partnerIds)
                  ->orWhereHas('partners', function ($partnerQuery) use ($partnerIds) {
                      $partnerQuery->whereIn('beneficiario_id', $partnerIds);
                  })
                  ->orWhereHas('user', function ($userQuery) use ($superPartner) {
                      $userQuery->where('super_partner_id', $superPartner->id);
                  });
            });
        }
        
        return $query->with('beneficiario:id,nombre')->paginate(request()->get('per_page', 10));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * When creating a client manually from admin panel:
     * - Do NOT activate eSIM automatically
     * - Do NOT create orders or subscriptions
     * - Just create the client record
     * - The can_activate_free_esim flag controls access to free eSIM
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

        [$beneficiario, $superPartner, $partnerIds] = $this->resolveClientOwnershipContext();

        // Simply save the client without automatic eSIM activation
        // The flag can_activate_free_esim controls whether they can activate it later
        //Valida si es un beneficiario que lo esta creando, si es asi asigna el beneficiario_id del cliente al beneficiario del usuario
        if ($beneficiario) {
                $request->merge(['beneficiario_id' => $beneficiario->id]);
        } elseif ($superPartner) {
            $request->merge(['super_partner_id' => $superPartner->id]);
            // Para super_partner ya no asignamos automáticamente el cliente al
            // "primer beneficiario". Se creará sin beneficiario primario y,
            // opcionalmente, se podrá asociar por pivot si se requiere.
        }

        $request->merge(['type' => 'cliente']);
        $cliente = $this->service->save($request->all());

        // Si el cliente fue creado por un super_partner, lo asociamos en la
        // tabla pivot con todos sus beneficiarios, para que tanto el
        // super_partner como sus partners compartan ese cliente sin que
        // cuente como cliente "primario" de un solo partner.
        if ($superPartner && !empty($partnerIds)) {
            $cliente->partners()->syncWithoutDetaching($partnerIds);
        }

        return created_responses('cliente');
    }

    private function resolveClientOwnershipContext(): array
    {
        $user = auth()->user();

        if (!$user) {
            return [null, null, []];
        }

        if ($user->user_type === 'beneficiario') {
            $beneficiario = Beneficiario::where('user_id', $user->id)->first();

            return [$beneficiario, null, $beneficiario ? [$beneficiario->id] : []];
        }

        if (in_array($user->user_type, ['super_partner', 'admin_partner'], true)) {
            $superPartner = $this->resolveScopedSuperPartner();
            $partnerIds = $superPartner
                ? $superPartner->beneficiarios()->pluck('id')->map(function ($id) {
                    return (int) $id;
                })->all()
                : [];

            return [null, $superPartner, $partnerIds];
        }

        return [null, null, []];
    }
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->service->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cliente $cliente)
    {
        $cliente = $this->service->update($cliente);

        return updated_responses('cliente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Cliente $cliente)
    {
        if ($this->service->delete($cliente)) {
            return deleted_responses('cliente');
        }
        return failed_responses();
    }

    /**
     * Toggle the can_activate_free_esim flag for a client.
     * 
     * @param Cliente $cliente
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleFreeEsim(Cliente $cliente)
    {
        $cliente->can_activate_free_esim = !$cliente->can_activate_free_esim;
        $cliente->save();
        
        $status = $cliente->can_activate_free_esim ? 'activado' : 'desactivado';
        
        return response()->json([
            'status' => true,
            'message' => "Permiso de eSIM gratuita {$status} exitosamente.",
            'data' => $cliente
        ]);
    }

    public function sendAccessEmail(Cliente $cliente)
    {
        $this->clienteAccessMailService->sendAccessCredentials($cliente);

        return response()->json([
            'status' => true,
            'message' => 'Correo enviado.',
        ]);
    }

    /**
     * Import clients from an Excel file.
     * Accepts columns: nombre, apellido, email (any order, extra columns ignored).
    * Password is set to identificador + '.'
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'free_esim_capacity' => 'required|integer|in:1,3,5,10',
        ]);

        $beneficiarioId = null;
        $partnerIds = [];
        $superPartnerId = null;
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            if ($beneficiario) {
                $beneficiarioId = $beneficiario->id;
                $partnerIds = [$beneficiario->id];
                $superPartnerId = $beneficiario->super_partner_id ? (int) $beneficiario->super_partner_id : null;
            }
        } elseif (($superPartner = $this->resolveScopedSuperPartner()) && $request->filled('beneficiario_id')) {
            // Super partner must specify a valid beneficiario from their partners
            $superPartnerId = (int) $superPartner->id;
            $partnerIds = $superPartner->beneficiarios()->pluck('id')->map(function ($id) {
                return (int) $id;
            })->all();
            $requestedId = (int) $request->input('beneficiario_id');
            $ownsPartner = $superPartner->beneficiarios()->where('id', $requestedId)->exists();
            if ($ownsPartner) {
                $beneficiarioId = $requestedId;
            }
        } elseif ($superPartner = $this->resolveScopedSuperPartner()) {
            $superPartnerId = (int) $superPartner->id;
            $partnerIds = $superPartner->beneficiarios()->pluck('id')->map(function ($id) {
                return (int) $id;
            })->all();
        } elseif ($request->filled('beneficiario_id')) {
            $beneficiarioId = $request->input('beneficiario_id');
            $partnerIds = [(int) $beneficiarioId];
            $selectedBeneficiario = \App\Models\App\Beneficiario\Beneficiario::find($beneficiarioId);
            $superPartnerId = $selectedBeneficiario && $selectedBeneficiario->super_partner_id
                ? (int) $selectedBeneficiario->super_partner_id
                : null;
        }

        $import = new ClienteImport($beneficiarioId, $partnerIds, (int) $request->input('free_esim_capacity'), $superPartnerId);
        Excel::import($import, $request->file('file'));

        return response()->json([
            'message' => "Importación completada: {$import->getImported()} clientes importados, {$import->getSkipped()} omitidos. eSIM gratis configurada en {$request->input('free_esim_capacity')} GB.",
            'imported' => $import->getImported(),
            'skipped'  => $import->getSkipped(),
            'errors'   => $import->getErrors(),
            'skipped_details' => $import->getSkippedDetails(),
        ]);
    }

    private function resolveScopedSuperPartner(): ?SuperPartner
    {
        if (!auth()->check()) {
            return null;
        }

        $user = auth()->user();

        if ($user->user_type === 'super_partner') {
            return SuperPartner::where('user_id', $user->id)->first();
        }

        if ($user->user_type === 'admin_partner' && $user->super_partner_id) {
            return SuperPartner::find($user->super_partner_id);
        }

        return null;
    }
}