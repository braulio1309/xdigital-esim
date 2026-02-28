<?php

namespace App\Http\Controllers\App\PaymentHistory;

use App\Filters\App\PaymentHistory\PaymentHistoryFilter;
use App\Http\Controllers\Controller;
use App\Models\App\PaymentHistory\PaymentHistory;
use App\Services\App\PaymentHistory\PaymentHistoryService;
use Illuminate\Support\Facades\Storage;

class PaymentHistoryController extends Controller
{
    /**
     * PaymentHistoryController constructor.
     * @param PaymentHistoryService $service
     * @param PaymentHistoryFilter $filter
     */
    public function __construct(PaymentHistoryService $service, PaymentHistoryFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    /**
     * Display a listing of payment histories.
     *
     * @return mixed
     */
    public function index()
    {
        $query = $this->service
            ->filters($this->filter)
            ->with('beneficiario');

        // Filter by beneficiario if user is a beneficiario
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            if ($beneficiario) {
                $query = $query->where('beneficiario_id', $beneficiario->id);
            }
        }

        return $query->latest()->paginate(request()->get('per_page', 10));
    }

    /**
     * Display the specified payment history.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->service
            ->with('beneficiario')
            ->find($id);
    }

    /**
     * Void (anular) a payment history: mark it as 'anulada' and revert
     * all linked transactions back to unpaid so the debt re-appears.
     *
     * @param PaymentHistory $paymentHistory
     * @return \Illuminate\Http\Response
     */
    public function cancel(PaymentHistory $paymentHistory)
    {
        // Only admins can void payment histories
        if (!auth()->check() || (auth()->user()->user_type !== 'admin' && !auth()->user()->hasRole('Admin'))) {
            return response()->json(['message' => 'Unauthorized. Only administrators can void payment histories.'], 403);
        }

        if ($paymentHistory->status === 'anulada') {
            return response()->json(['message' => 'Este historial ya fue anulado.'], 422);
        }

        // Mark payment history as voided
        $paymentHistory->update([
            'status' => 'anulada',
            'cancelled_at' => now(),
        ]);

        // Revert all transactions that were marked paid by this record
        \App\Models\App\Transaction\Transaction::where('payment_history_id', $paymentHistory->id)
            ->update([
                'is_paid' => false,
                'paid_at' => null,
                'payment_history_id' => null,
            ]);

        return response()->json([
            'message' => 'Historial de pago anulado correctamente. Las transacciones vuelven a aparecer como deuda.',
        ]);
    }

    /**
     * Remove the specified payment history from storage.
     *
     * @param PaymentHistory $paymentHistory
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(PaymentHistory $paymentHistory)
    {
        // Delete the support file if it exists
        if ($paymentHistory->support_path && Storage::disk('public')->exists($paymentHistory->support_path)) {
            Storage::disk('public')->delete($paymentHistory->support_path);
        }

        if ($this->service->delete($paymentHistory)) {
            return deleted_responses('payment_history');
        }
        return failed_responses();
    }

    /**
     * Download the support file for a payment history.
     *
     * @param PaymentHistory $paymentHistory
     * @return \Illuminate\Http\Response
     */
    public function downloadSupport(PaymentHistory $paymentHistory)
    {
        if (!$paymentHistory->support_path || !Storage::disk('public')->exists($paymentHistory->support_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::disk('public')->download(
            $paymentHistory->support_path,
            $paymentHistory->support_original_name ?? basename($paymentHistory->support_path)
        );
    }
}

