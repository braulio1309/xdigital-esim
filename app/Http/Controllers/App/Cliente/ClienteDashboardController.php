<?php

namespace App\Http\Controllers\App\Cliente;

use App\Http\Controllers\Controller;
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

    /**
     * Show the cliente dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Ensure user is a cliente
        if ($user->user_type !== 'cliente') {
            abort(403, 'Unauthorized access');
        }
        
        $cliente = $user->cliente;
        
        if (!$cliente) {
            abort(404, 'Cliente not found');
        }
        
        // Get active plan (latest transaction)
        $activePlan = $cliente->active_plan;
        
        // Get all transactions
        $transactions = $cliente->transactions()
            ->orderBy('creation_time', 'desc')
            ->get();
        
        $data = [
            'cliente' => $cliente,
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

        if ($user->user_type !== 'cliente' || !$user->cliente || (int) $transaction->cliente_id !== (int) $user->cliente->id) {
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

        if ($user->user_type !== 'cliente') {
            return redirect()->back()->with('error', 'Solo los clientes pueden enviar este correo.');
        }

        if (!$user->cliente) {
            return redirect()->back()->with('error', 'No se encontró el perfil del cliente.');
        }

        if ((int) $transaction->cliente_id !== (int) $user->cliente->id) {
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
        
        if ($user->user_type !== 'cliente') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $cliente = $user->cliente;
        
        if (!$cliente) {
            return response()->json(['error' => 'Cliente not found'], 404);
        }
        
        $activePlan = $cliente->active_plan;
        $transactions = $cliente->transactions()
            ->orderBy('creation_time', 'desc')
            ->get();
        
        return response()->json([
            'cliente' => [
                'nombre' => $cliente->nombre,
                'apellido' => $cliente->apellido,
                'email' => $cliente->email,
            ],
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
