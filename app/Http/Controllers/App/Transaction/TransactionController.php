<?php

namespace App\Http\Controllers\App\Transaction;

use App\Filters\App\Transaction\TransactionFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\TransactionRequest as Request;
use App\Models\App\PaymentHistory\PaymentHistory;
use App\Models\App\Transaction\Transaction;
use App\Services\App\Transaction\TransactionService;
use Illuminate\Support\Facades\Storage;

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
                $validated['start_date'],
                $validated['end_date'] . ' 23:59:59'
            ])
            ->update([
                'is_paid' => true,
                'paid_at' => now()
            ]);

        // Save payment history record
        $supportPath = null;
        $supportOriginalName = null;
        if ($request->hasFile('support')) {
            $file = $request->file('support');
            $supportOriginalName = $file->getClientOriginalName();
            $supportPath = $file->store('payment-supports', 'public');
        }

        PaymentHistory::create([
            'beneficiario_id' => $validated['beneficiario_id'],
            'reference' => $validated['reference'] ?? null,
            'payment_date' => $validated['payment_date'],
            'support_path' => $supportPath,
            'support_original_name' => $supportOriginalName,
            'amount' => $updated * 0.85, // $0.85 is the commission rate per free eSIM transaction
            'transactions_count' => $updated,
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'message' => "Successfully marked {$updated} transactions as paid",
            'updated_count' => $updated
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
