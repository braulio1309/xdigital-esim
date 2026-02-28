<?php

namespace App\Http\Controllers\App\Transaction;

use App\Filters\App\Transaction\TransactionFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\TransactionRequest as Request;
use App\Models\App\PaymentHistory\PaymentHistory;
use App\Models\App\Transaction\Transaction;
use App\Services\App\Transaction\TransactionService;
use App\Services\EsimFxService;
use App\Exports\App\Transaction\TransactionExport;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    /**
     * TransactionController constructor.
     * @param TransactionService $service
     * @param TransactionFilter $filter
     */
    public function __construct(TransactionService $service, TransactionFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $query = $this->service
            ->filters($this->filter)
            ->with('cliente.beneficiario.planMargins');
        
        // Filter by beneficiario_id if user is a beneficiario
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            
            if ($beneficiario) {
                $query = $query->whereHas('cliente', function ($q) use ($beneficiario) {
                    $q->where('beneficiario_id', $beneficiario->id);
                });
            }
        }
        
        $transactions = $query->latest()->paginate(request()->get('per_page', 10));
        
        // Add commission calculations to each transaction
        $transactions->getCollection()->transform(function ($transaction) {
            $transaction->commission_amount = $transaction->getCommissionAmount();
            $transaction->commission_percentage = $transaction->getCommissionPercentage();
            $transaction->beneficiario = $transaction->cliente->beneficiario ?? null;
            return $transaction;
        });
        
        return $transactions;
    }

    /**
     * Get payment statistics for unpaid transactions
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentStats()
    {
        $query = Transaction::with('cliente.beneficiario')
            ->where('is_paid', false)
            ->where('purchase_amount', 0); // Only free eSIMs
        
        // Filter by beneficiario if not admin
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            
            if ($beneficiario) {
                $query = $query->whereHas('cliente', function ($q) use ($beneficiario) {
                    $q->where('beneficiario_id', $beneficiario->id);
                });
            }
        }
        
        $unpaidCount = $query->count();
        $totalOwed = $unpaidCount * 0.85;
        
        return response()->json([
            'unpaid_count' => $unpaidCount,
            'total_owed' => $totalOwed
        ]);
    }

    /**
     * Calculate payment amount for unpaid free eSIM transactions
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function calculatePaymentAmount(\Illuminate\Http\Request $request)
    {
        $beneficiarioId = $request->get('beneficiario_id');
        $startDateRaw = $request->get('start_date');
        $endDateRaw = $request->get('end_date');

        
        $startDateClean = preg_replace('/\s\([^)]+\)/', '', $startDateRaw);
        $endDateClean = preg_replace('/\s\([^)]+\)/', '', $endDateRaw);

        try {
            // 3. Parseamos a Carbon para poder manipular la fecha o guardarla en BD
            $startDate = Carbon::parse($startDateClean);
            $endDate = Carbon::parse($endDateClean);

            // Opcional: Si necesitas guardarlo en MySQL, usa format('Y-m-d H:i:s')
            // $dbDate = $startDate->toDateTimeString();

        } catch (\Exception $e) {
            // Manejo de error si la fecha de plano es ilegible
            return response()->json(['error' => 'Formato de fecha inválido'], 422);
        }

        $query = Transaction::where('purchase_amount', 0)
            ->where('is_paid', false);

        if ($beneficiarioId) {
            $query->whereHas('cliente', function ($q) use ($beneficiarioId) {
                $q->where('beneficiario_id', $beneficiarioId);
            });
        }

        if ($startDate && $endDate) {
            $query->whereBetween('creation_time', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($startDate) {
            $query->where('creation_time', '>=', Carbon::parse($startDate)->startOfDay());
        } elseif ($endDate) {
            $query->where('creation_time', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $count = $query->count();
        $amount = round($count * 0.85, 2); // $0.85 is the commission rate per free eSIM transaction

        return response()->json([
            'count' => $count,
            'amount' => $amount
        ]);
    }

    /**
     * Mark transactions as paid
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function markAsPaid(\Illuminate\Http\Request $request)
    {
        // Authorization check - only admin users can mark transactions as paid
        if (!auth()->check() || (auth()->user()->user_type !== 'admin' && !auth()->user()->hasRole('Admin'))) {
            return response()->json(['message' => 'Unauthorized. Only administrators can mark transactions as paid.'], 403);
        }

        $dateFields = ['start_date', 'end_date', 'payment_date'];

        // 2. Iteramos y limpiamos cada uno si existe en el request
        foreach ($dateFields as $field) {
            if ($request->filled($field)) {
                // Quitamos todo lo que esté entre paréntesis, ej: " (hora de Venezuela)"
                $cleanString = preg_replace('/\s*\(.*?\)/', '', $request->input($field));
                
                // Lo parseamos con Carbon y lo devolvemos al request en formato estándar
                $request->merge([
                    $field => Carbon::parse($cleanString)->format('Y-m-d H:i:s')
                ]);
            }
        }
        $validated = $request->validate([
            'beneficiario_id' => 'required|exists:beneficiarios,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reference' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'support' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        $updated = Transaction::where('purchase_amount', 0) // Only free eSIMs
            ->where('is_paid', false)
            ->whereHas('cliente', function ($q) use ($validated) {
                $q->where('beneficiario_id', $validated['beneficiario_id']);
            })
            ->whereBetween('creation_time', [
                Carbon::parse($validated['start_date'])->startOfDay(),
                Carbon::parse($validated['end_date'])->endOfDay()
            ])
            ->get();

        $updatedCount = $updated->count();

        // Save payment history record first so we can link transactions back to it
        $supportPath = null;
        $supportOriginalName = null;
        if ($request->hasFile('support')) {
            $file = $request->file('support');
            $supportOriginalName = $file->getClientOriginalName();
            $supportPath = $file->store('payment-supports', 'public');
        }

        $paymentHistory = PaymentHistory::create([
            'beneficiario_id' => $validated['beneficiario_id'],
            'reference' => $validated['reference'] ?? null,
            'payment_date' => $validated['payment_date'],
            'support_path' => $supportPath,
            'support_original_name' => $supportOriginalName,
            'amount' => $updatedCount * 0.85, // $0.85 is the commission rate per free eSIM transaction
            'transactions_count' => $updatedCount,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Mark transactions as paid and link to this payment history record
        Transaction::whereIn('id', $updated->pluck('id'))
            ->update([
                'is_paid' => true,
                'paid_at' => now(),
                'payment_history_id' => $paymentHistory->id,
            ]);

        return response()->json([
            'message' => "Successfully marked {$updatedCount} transactions as paid",
            'updated_count' => $updatedCount
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $transaction = $this->service->save();

        return created_responses('transaction');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->service
            ->with('cliente')
            ->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        $transaction = $this->service->update($transaction);

        return updated_responses('transaction');
    }

    /**
     * Get eSIM status from external API
     *
     * @param Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function esimStatus(Transaction $transaction)
    {
        if (empty($transaction->iccid)) {
            return response()->json(['message' => 'This transaction does not have an ICCID.'], 422);
        }

        try {
            $esimService = app(EsimFxService::class);
            $data = $esimService->getEsimStatus($transaction->iccid);
            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Terminate eSIM subscription via external API and store terminated_at
     *
     * @param Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function terminateSubscription(Transaction $transaction)
    {
        if (empty($transaction->order_id)) {
            return response()->json(['message' => 'This transaction does not have an order ID.'], 422);
        }

        try {
            $esimService = app(EsimFxService::class);
            $esimService->terminateSubscription($transaction->order_id);
            $transaction->update(['terminated_at' => now()]);
            return response()->json(['message' => 'Subscription terminated successfully.', 'terminated_at' => $transaction->terminated_at]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export transactions to Excel applying the same filters as the index.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(\Illuminate\Http\Request $request)
    {
        $filters = $request->only([
            'beneficiario_id',
            'type',
            'payment_status',
            'start_date',
            'end_date',
            'search',
        ]);

        $filename = 'transacciones-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new TransactionExport($filters), $filename);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Transaction $transaction)
    {
        if ($this->service->delete($transaction)) {
            return deleted_responses('transaction');
        }
        return failed_responses();
    }
}
