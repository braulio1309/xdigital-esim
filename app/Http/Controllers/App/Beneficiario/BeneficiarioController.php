<?php

namespace App\Http\Controllers\App\Beneficiario;

use App\Exports\App\Beneficiario\BeneficiarioCommissionsExport;
use App\Filters\App\Beneficiario\BeneficiarioFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\BeneficiarioRequest as Request;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\Core\Status;
use App\Services\App\Beneficiario\BeneficiarioService;
use App\Services\App\Settings\BeneficiaryPlanMarginService;
use App\Services\App\Settings\PlanMarginService;
use App\Services\EsimFxService;
use App\Models\App\Transaction\Transaction;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class BeneficiarioController extends Controller
{
    /**
     * BeneficiarioController constructor.
     * @param BeneficiarioService $service
     * @param BeneficiarioFilter $filter
     */
    public function __construct(BeneficiarioService $service, BeneficiarioFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $query = $this->service->with('user.status:id,name,class')->filters($this->filter)->latest();

        // Scope by super partner network for super_partner and admin_partner users
        if (auth()->check() && in_array(auth()->user()->user_type, ['super_partner', 'admin_partner'], true)) {
            $user = auth()->user();
            $superPartner = $user->user_type === 'super_partner'
                ? \App\Models\App\SuperPartner\SuperPartner::where('user_id', auth()->id())->first()
                : \App\Models\App\SuperPartner\SuperPartner::find($user->super_partner_id);

            if ($superPartner) {
                $query = $query->where('super_partner_id', $superPartner->id);
            }
        }

        $beneficiarios = $query->paginate(request()->get('per_page', 10));
        
        // Add unpaid transactions count and total owed for each beneficiary
        $beneficiarios->getCollection()->transform(function ($beneficiario) {
            $unpaidTransactions = Transaction::with(['beneficiario', 'cliente.beneficiario', 'superPartner'])
                ->where('is_paid', false)
                ->where('beneficiario_id', $beneficiario->id)
                ->get();

            $beneficiario->unpaid_transactions_count = $unpaidTransactions->count();
            $beneficiario->total_owed = round($unpaidTransactions->sum(function (Transaction $transaction) {
                return $transaction->getCommissionAmount();
            }), 2);
            
            return $beneficiario;
        });
        
        return $beneficiarios;
    }

    public function inactivate(Beneficiario $beneficiario)
    {
        if (!$this->canManageStatus($beneficiario)) {
            return response()->json([
                'status' => false,
                'message' => 'No autorizado para inactivar este partner.',
            ], 403);
        }

        return $this->updatePartnerStatus($beneficiario, 'status_inactive', 'inactivo', 'inactivado');
    }

    public function activate(Beneficiario $beneficiario)
    {
        if (!$this->canManageStatus($beneficiario)) {
            return response()->json([
                'status' => false,
                'message' => 'No autorizado para activar este partner.',
            ], 403);
        }

        return $this->updatePartnerStatus($beneficiario, 'status_active', 'activo', 'activado');
    }

    private function updatePartnerStatus(Beneficiario $beneficiario, string $statusName, string $statusLabel, string $actionLabel)
    {

        $beneficiario->loadMissing('user.status');

        if (!$beneficiario->user) {
            return response()->json([
                'status' => false,
                'message' => 'El partner no tiene un usuario asociado.',
            ], 422);
        }

        if (optional($beneficiario->user->status)->name === $statusName) {
            return response()->json([
                'status' => false,
                'message' => "El partner ya está {$statusLabel}.",
            ], 422);
        }

        $status = Status::findByNameAndType($statusName, 'user');

        if (!$status) {
            return response()->json([
                'status' => false,
                'message' => "No se encontró el estado {$statusLabel}.",
            ], 422);
        }

        $beneficiario->user->markAs($status);

        return response()->json([
            'status' => true,
            'message' => "Partner {$actionLabel} exitosamente.",
        ]);
    }

    /**
     * Download Excel file with commissions for a specific beneficiary.
     *
     * @param Beneficiario $beneficiario
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCommissions(
        Beneficiario $beneficiario,
        EsimFxService $esimService,
        PlanMarginService $planMarginService,
        BeneficiaryPlanMarginService $beneficiaryPlanMarginService
    ) {
        $export = new BeneficiarioCommissionsExport(
            $beneficiario->id,
            $beneficiario->nombre,
            $esimService,
            $planMarginService,
            $beneficiaryPlanMarginService
        );

        $filename = 'comisiones-' . Str::slug($beneficiario->nombre) . '.xlsx';

        return Excel::download($export, $filename);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // If a super_partner is creating a beneficiario, set super_partner_id
        if (auth()->check() && auth()->user()->user_type === 'super_partner') {
            $superPartner = \App\Models\App\SuperPartner\SuperPartner::where('user_id', auth()->id())->first();
            if ($superPartner) {
                $request->merge(['super_partner_id' => $superPartner->id]);
            }
        }

        $beneficiario = $this->service->save($request->all());

        return created_responses('beneficiario');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->service->find($id)->load('user:id,email,last_name');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Beneficiario $beneficiario)
    {
        $beneficiario = $this->service->update($beneficiario);

        return updated_responses('beneficiario');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Beneficiario $beneficiario)
    {
        if ($this->service->delete($beneficiario)) {
            return deleted_responses('beneficiario');
        }
        return failed_responses();
    }

    /**
     * Get visual commission configuration for a beneficiario (partner).
     * Accessible by admin and the super partner who owns this partner.
     *
     * @param  Beneficiario $beneficiario
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVisualCommissions(Beneficiario $beneficiario)
    {
        if (!$this->canManageVisualCommissions($beneficiario)) {
            return response()->json([
                'status' => false,
                'message' => 'No autorizado para ver las comisiones de este partner.',
            ], 403);
        }

        return response()->json([
            'sale_commission_latam_pct'     => $beneficiario->sale_commission_latam_pct !== null ? (float) $beneficiario->sale_commission_latam_pct : null,
            'sale_commission_usa_ca_eu_pct' => $beneficiario->sale_commission_usa_ca_eu_pct !== null ? (float) $beneficiario->sale_commission_usa_ca_eu_pct : null,
        ]);
    }

    /**
     * Update visual commission configuration for a beneficiario (partner).
     * Accessible by admin and the super partner who owns this partner.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Beneficiario $beneficiario
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateVisualCommissions(\Illuminate\Http\Request $request, Beneficiario $beneficiario)
    {
        if (!$this->canManageVisualCommissions($beneficiario)) {
            return response()->json([
                'status' => false,
                'message' => 'No autorizado para editar las comisiones de este partner.',
            ], 403);
        }

        $validated = $request->validate([
            'sale_commission_latam_pct'     => 'nullable|numeric|min:0|max:100',
            'sale_commission_usa_ca_eu_pct' => 'nullable|numeric|min:0|max:100',
        ]);

        if (array_key_exists('sale_commission_latam_pct', $validated)) {
            $beneficiario->sale_commission_latam_pct = $validated['sale_commission_latam_pct'];
        }

        if (array_key_exists('sale_commission_usa_ca_eu_pct', $validated)) {
            $beneficiario->sale_commission_usa_ca_eu_pct = $validated['sale_commission_usa_ca_eu_pct'];
        }

        $beneficiario->save();

        return response()->json([
            'message'                       => __('default.updated_response', ['name' => 'Comisiones Visuales']),
            'sale_commission_latam_pct'     => $beneficiario->sale_commission_latam_pct !== null ? (float) $beneficiario->sale_commission_latam_pct : null,
            'sale_commission_usa_ca_eu_pct' => $beneficiario->sale_commission_usa_ca_eu_pct !== null ? (float) $beneficiario->sale_commission_usa_ca_eu_pct : null,
        ]);
    }

    private function canManageVisualCommissions(Beneficiario $beneficiario): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        if ($user->user_type === 'admin') {
            return true;
        }

        if ($user->user_type === 'super_partner') {
            $superPartner = SuperPartner::where('user_id', $user->id)->first();

            return $superPartner
                ? (int) $beneficiario->super_partner_id === (int) $superPartner->id
                : false;
        }

        return false;
    }

    private function canManageStatus(Beneficiario $beneficiario): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        if ($user->user_type === 'admin') {
            return true;
        }

        if ($user->user_type === 'super_partner') {
            $superPartner = SuperPartner::where('user_id', $user->id)->first();

            return $superPartner
                ? (int) $beneficiario->super_partner_id === (int) $superPartner->id
                : false;
        }

        if ($user->user_type === 'admin_partner' && $user->super_partner_id) {
            return (int) $beneficiario->super_partner_id === (int) $user->super_partner_id;
        }

        return false;
    }
}
