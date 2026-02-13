<?php

namespace App\Http\Controllers\App\Transaction;

use App\Filters\App\Transaction\TransactionFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\TransactionRequest as Request;
use App\Models\App\Transaction\Transaction;
use App\Services\App\Transaction\TransactionService;

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
            ->with('cliente');
        
        // Filter by beneficiario_id if user is a beneficiario
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            
            if ($beneficiario) {
                $query = $query->whereHas('cliente', function ($q) use ($beneficiario) {
                    $q->where('beneficiario_id', $beneficiario->id);
                });
            }
        }
        
        return $query->latest()
            ->paginate(request()->get('per_page', 10));
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
