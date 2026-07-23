<?php

namespace App\Http\Controllers\App\Cliente;

use App\Http\Controllers\Controller;
use App\Models\App\Cliente\Cliente;
use App\Mail\App\Cliente\EsimRechargeReminderMail;
use App\Models\App\Transaction\Transaction;
use App\Services\EsimFxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ClienteDashboardController extends Controller
{
    private const MANUAL_RECHARGE_LINK_TTL_HOURS = 24;

    private function resolveClienteEmail($user): string
    {
        return mb_strtolower(trim((string) ($user->email ?? '')));
    }

    private function resolveClienteProfile($user): ?Cliente
    {
        if (!$user) {
            return null;
        }

        $cliente = $user->cliente;

        if ($cliente) {
            return $cliente;
        }

        $email = $this->resolveClienteEmail($user);

        if ($email === '') {
            return null;
        }

        $cliente = Cliente::query()
            ->withCount('transactions')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->orderByDesc('transactions_count')
            ->latest('id')
            ->first();

        if ($cliente && (int) ($cliente->user_id ?? 0) !== (int) $user->id) {
            $cliente->user_id = $user->id;
            $cliente->save();
        }

        return $cliente;
    }

    private function resolveClienteTransactions($user)
    {
        $email = $this->resolveClienteEmail($user);

        if ($email === '') {
            return collect();
        }

        return Transaction::query()
            ->with('cliente')
            ->whereHas('cliente', function ($query) use ($email) {
                $query->whereRaw('LOWER(email) = ?', [$email]);
            })
            ->orderBy('creation_time', 'desc')
            ->get();
    }

    private function resolveActivePlanFromTransactions($transactions): ?Transaction
    {
        return $transactions
            ->where('status', 'completed')
            ->sortByDesc(function ($transaction) {
                return optional($transaction->creation_time)->timestamp ?? 0;
            })
            ->first();
    }

    private function canAccessTransactionForClienteEmail($user, Transaction $transaction): bool
    {
        $email = $this->resolveClienteEmail($user);

        if ($email === '') {
            return false;
        }

        return $transaction->cliente()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->exists();
    }

    private function canAccessClienteArea($user): bool
    {
        return (bool) $user;
    }

    private function buildDashboardIdentity($user, ?Cliente $cliente): array
    {
        if ($cliente) {
            return [
                'nombre' => $cliente->nombre,
                'apellido' => $cliente->apellido,
                'email' => $cliente->email,
            ];
        }

        return [
            'nombre' => (string) ($user->first_name ?? ''),
            'apellido' => (string) ($user->last_name ?? ''),
            'email' => (string) ($user->email ?? ''),
        ];
    }

    /**
     * Show the cliente dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Ensure user can access the cliente area
        if (!$this->canAccessClienteArea($user)) {
            abort(403, 'Unauthorized access');
        }
        
        $cliente = $this->resolveClienteProfile($user);
        $dashboardIdentity = $this->buildDashboardIdentity($user, $cliente);
        
        $transactions = $this->resolveClienteTransactions($user);
        $activePlan = $this->resolveActivePlanFromTransactions($transactions);
        
        $data = [
            'cliente' => $dashboardIdentity,
            'active_plan' => $activePlan,
            'transactions' => $transactions,
        ];
        
        return view('dashboard.cliente', $data);
    }

    /**
     * Return detail for a transaction owned by the authenticated client.
     *
     * @param Request $request
     * @param Transaction $transaction
     * @param EsimFxService $esimFxService
     * @return \Illuminate\Http\JsonResponse
     */
    public function transactionDetail(Request $request, Transaction $transaction, EsimFxService $esimFxService)
    {
        $user = $request->user();
        $cliente = $this->resolveClienteProfile($user);

        if (!$this->canAccessClienteArea($user) || !$this->canAccessTransactionForClienteEmail($user, $transaction)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (empty($transaction->order_id)) {
            return response()->json(['message' => 'Esta transacción no tiene order ID.'], 422);
        }

        try {
            $data = $esimFxService->getOrder($transaction->order_id);

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Send a manual recharge email for a specific transaction.
     *
     * @param Request $request
     * @param Transaction $transaction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendRechargeEmail(Request $request, Transaction $transaction)
    {
        $user = $request->user();

        if (!$this->canAccessClienteArea($user)) {
            return redirect()->back()->with('error', 'No autorizado.');
        }

        if (!$this->canAccessTransactionForClienteEmail($user, $transaction)) {
            return redirect()->back()->with('error', 'No autorizado para enviar correo de esta transacción.');
        }

        if (empty($transaction->iccid) || empty($transaction->cliente) || empty($transaction->cliente->email)) {
            return redirect()->back()->with('error', 'La transacción no tiene datos suficientes para enviar el correo.');
        }

        try {
            $magicToken = (string) Str::uuid();
            $expiration = now()->addHours(self::MANUAL_RECHARGE_LINK_TTL_HOURS);
            Cache::put('manual_recharge_link:' . $magicToken, [
                'transaction_id' => (int) $transaction->id,
            ], $expiration);

            $rechargeLink = URL::temporarySignedRoute('planes.recharge-link', $expiration, [
                'token' => $magicToken,
            ]);

            Mail::to($transaction->cliente->email)->send(new EsimRechargeReminderMail(
                $transaction,
                $rechargeLink
            ));

            return redirect()->back()->with('success', 'Correo de recarga enviado correctamente.');
        } catch (\Throwable $exception) {
            Log::error('Error sending manual recharge email from cliente dashboard.', [
                'transaction_id' => $transaction->id,
                'cliente_id' => $transaction->cliente_id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->back()->with('error', 'No fue posible enviar el correo de recarga.');
        }
    }

    /**
     * Resolve a temporary signed recharge link and redirect to plans page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function openRechargeLink(Request $request)
    {
        $token = (string) $request->query('token', '');

        if (!Str::isUuid($token)) {
            abort(403);
        }

        $cachedLinkPayload = Cache::pull('manual_recharge_link:' . $token);

        if (!is_array($cachedLinkPayload) || empty($cachedLinkPayload['transaction_id'])) {
            abort(403);
        }

        $transaction = Transaction::query()->find((int) $cachedLinkPayload['transaction_id']);

        if (!$transaction || empty($transaction->iccid)) {
            abort(404);
        }

        return redirect()->route('planes.index', [
            'recharge_iccid' => $transaction->iccid,
        ]);
    }

    /**
     * Get cliente dashboard data as JSON (for API/AJAX)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $user = $request->user();
        
        if (!$this->canAccessClienteArea($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $cliente = $this->resolveClienteProfile($user);
        $dashboardIdentity = $this->buildDashboardIdentity($user, $cliente);
        
        $transactions = $this->resolveClienteTransactions($user);
        $activePlan = $this->resolveActivePlanFromTransactions($transactions);
        
        return response()->json([
            'cliente' => $dashboardIdentity,
            'active_plan' => $activePlan ? [
                'transaction_id' => $activePlan->transaction_id,
                'status' => $activePlan->status,
                'iccid' => $activePlan->iccid,
                'creation_time' => $activePlan->creation_time,
            ] : null,
            'transactions' => $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'transaction_id' => $transaction->transaction_id,
                    'status' => $transaction->status,
                    'iccid' => $transaction->iccid,
                    'creation_time' => $transaction->creation_time,
                ];
            }),
        ]);
    }
}
