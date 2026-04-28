<?php

namespace App\Http\Controllers\App\Transaction;

use App\Models\App\Beneficiario\Beneficiario;
use App\Filters\App\Transaction\TransactionFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\TransactionRequest as Request;
use App\Mail\App\Cliente\EsimRechargeMail;
use App\Models\App\PaymentHistory\PaymentHistory;
use App\Models\App\Transaction\Transaction;
use App\Services\App\Transaction\TransactionService;
use App\Services\EsimFxService;
use App\Exports\App\Transaction\TransactionExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            ->with('cliente.beneficiario.planMargins', 'beneficiario.planMargins', 'superPartner');

        // Filter by beneficiario_id if user is a beneficiario
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            
            if ($beneficiario) {
                $query = $query->where('beneficiario_id', $beneficiario->id);
            }
        } elseif (auth()->check() && auth()->user()->user_type === 'super_partner') {
            $superPartner = \App\Models\App\SuperPartner\SuperPartner::where('user_id', auth()->id())->first();
            if ($superPartner) {
                $partnerIds = $superPartner->beneficiarios()->pluck('id');

                // Super partner solo ve transacciones de sus propios beneficiarios.
                $query = $query->where(function ($builder) use ($partnerIds, $superPartner) {
                    $builder->whereIn('beneficiario_id', $partnerIds)
                        ->orWhere('super_partner_id', $superPartner->id);
                });
            }
        }
        
        $transactions = $query->latest()->paginate(request()->get('per_page', 10));
        
        // Add commission calculations to each transaction
        $transactions->getCollection()->transform(function ($transaction) {
            $transaction->commission_amount = $transaction->getCommissionAmount();
            $transaction->commission_percentage = $transaction->getCommissionPercentage();
            $transaction->beneficiario = $transaction->beneficiario ?? ($transaction->cliente->beneficiario ?? null);
            $transaction->super_partner_name = $transaction->superPartner ? $transaction->superPartner->nombre : null;
            $transaction->partner_name = $transaction->beneficiario
                ? $transaction->beneficiario->nombre
                : ($transaction->superPartner ? 'SP: ' . $transaction->superPartner->nombre : 'N/A');
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
        $query = Transaction::with(['beneficiario', 'cliente.beneficiario'])
            ->where('is_paid', false);

        if ($requestBeneficiarioId = request()->get('beneficiario_id')) {
            if ($requestBeneficiarioId === 'none') {
                $query->whereNull('beneficiario_id');
            } else {
                $query->where('beneficiario_id', $requestBeneficiarioId);
            }
        }

        if ($requestSuperPartnerId = request()->get('super_partner_id')) {
            $query->where('super_partner_id', $requestSuperPartnerId);
        }

        if ($startDateRaw = request()->get('start_date')) {
            $cleanDate = preg_replace('/\s*\(.*?\)/', '', $startDateRaw);
            $query->where('creation_time', '>=', Carbon::parse($cleanDate)->startOfDay());
        }

        if ($endDateRaw = request()->get('end_date')) {
            $cleanDate = preg_replace('/\s*\(.*?\)/', '', $endDateRaw);
            $query->where('creation_time', '<=', Carbon::parse($cleanDate)->endOfDay());
        }

        if ($type = request()->get('type')) {
            if ($type === 'free') {
                $query->where('purchase_amount', 0);
            } elseif ($type === 'paid') {
                $query->where('purchase_amount', '>', 0);
            }
        }
        
        // Filter by beneficiario if not admin
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            
            if ($beneficiario) {
                $query = $query->where('beneficiario_id', $beneficiario->id);
            }
        } elseif (auth()->check() && auth()->user()->user_type === 'super_partner') {
            $superPartner = \App\Models\App\SuperPartner\SuperPartner::where('user_id', auth()->id())->first();
            if ($superPartner) {
                $partnerIds = $superPartner->beneficiarios()->pluck('id');
                // Super partner solo ve transacciones de los beneficiarios que pertenecen a su red.
                $query = $query->where(function ($builder) use ($partnerIds, $superPartner) {
                    $builder->whereIn('beneficiario_id', $partnerIds)
                        ->orWhere('super_partner_id', $superPartner->id);
                });
            }
        }
        
        $transactions = $query->get();

        $unpaidCount = $transactions->count();
        $totalOwed = $transactions->sum(function (Transaction $transaction) {
            return $transaction->getCommissionAmount();
        });
        
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
        $superPartnerId = $request->get('super_partner_id');
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

        $query = Transaction::where('is_paid', false);

        // Filtro explícito de beneficiario desde el front
        if ($beneficiarioId) {
            if ($beneficiarioId === 'none') {
                $query->whereNull('beneficiario_id');
            } else {
                $query->where('beneficiario_id', $beneficiarioId);
            }
        }

        if ($superPartnerId) {
            $query->where('super_partner_id', $superPartnerId);
        }

        if ($type = $request->get('type')) {
            if ($type === 'free') {
                $query->where('purchase_amount', 0);
            } elseif ($type === 'paid') {
                $query->where('purchase_amount', '>', 0);
            }
        }

        // Alcance por tipo de usuario para evitar que super_partner vean
        // datos de todo el sistema.
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            if ($beneficiario) {
                $query->where('beneficiario_id', $beneficiario->id);
            }
        } elseif (auth()->check() && auth()->user()->user_type === 'super_partner') {
            $superPartner = \App\Models\App\SuperPartner\SuperPartner::where('user_id', auth()->id())->first();
            if ($superPartner) {
                $partnerIds = $superPartner->beneficiarios()->pluck('id');
                // Super partner solo ve transacciones de sus beneficiarios,
                // incluso cuando se filtra por rango de fechas o por beneficiario.
                $query->where(function ($builder) use ($partnerIds, $superPartner) {
                    $builder->whereIn('beneficiario_id', $partnerIds)
                        ->orWhere('super_partner_id', $superPartner->id);
                });
            }
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

        $transactions = $query->with(['beneficiario', 'cliente.beneficiario'])->get();

        $count = $transactions->count();
        $amount = round($transactions->sum(function (Transaction $transaction) {
            return $transaction->getCommissionAmount();
        }), 2);

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

        $updated = Transaction::where('is_paid', false)
            ->where('beneficiario_id', $validated['beneficiario_id'])
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

        // Calculate total amount based on stored commission per transaction
        $totalAmount = round($updated->sum(function (Transaction $transaction) {
            return $transaction->getCommissionAmount();
        }), 2);

        $paymentHistory = PaymentHistory::create([
            'beneficiario_id' => $validated['beneficiario_id'],
            'reference' => $validated['reference'] ?? null,
            'payment_date' => $validated['payment_date'],
            'support_path' => $supportPath,
            'support_original_name' => $supportOriginalName,
            'amount' => $totalAmount,
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
        if (empty($transaction->order_id)) {
            return response()->json(['message' => 'This transaction does not have an order ID.'], 422);
        }

        try {
            $esimService = app(EsimFxService::class);
            $data = $esimService->getOrder($transaction->order_id);
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
            'super_partner_id',
            'type',
            'payment_status',
            'start_date',
            'end_date',
            'search',
        ]);

        // Clean date strings that may contain browser timezone info
        // e.g. "Sun Mar 01 2026 15:33:13 GMT-0400 (hora de Venezuela)"
        foreach (['start_date', 'end_date'] as $field) {
            if (!empty($filters[$field])) {
                $filters[$field] = preg_replace('/\s*\(.*?\)/', '', $filters[$field]);
            }
        }

        $filename = 'transacciones-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new TransactionExport($filters), $filename);
    }

    /**
     * Recharge an existing eSIM with a new data top-up (admin only).
     *
     * @param \Illuminate\Http\Request $request
     * @param Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function recharge(\Illuminate\Http\Request $request, Transaction $transaction)
    {
        if (!auth()->check() || (auth()->user()->user_type !== 'admin' && !auth()->user()->hasRole('Admin'))) {
            return response()->json(['message' => 'Unauthorized. Only administrators can recharge eSIMs.'], 403);
        }

        $validated = $request->validate([
            'gb_amount' => 'required|integer|in:1,3,5,10',
        ]);

        $gbAmount = (int) $validated['gb_amount'];

        if (empty($transaction->iccid)) {
            return response()->json(['message' => 'This transaction does not have an ICCID and cannot be recharged.'], 422);
        }

        try {
            $esimService = app(EsimFxService::class);

            // Try to determine the country from the plan_name for better product matching.
            // Plan names typically include a 2-letter country code (e.g. "US 3GB 30 Days").
            $countryCode = null;
            if (!empty($transaction->plan_name)) {
                if (preg_match('/\b([A-Z]{2})\b/', strtoupper($transaction->plan_name), $matches)) {
                    $countryCode = $matches[1];
                }
            }

            $productFilters = $countryCode ? ['countries' => $countryCode] : [];
            $products = $esimService->getProducts($productFilters);

            // Find a product matching the requested GB amount
            $selectedProduct = collect($products)
                ->filter(function ($product) use ($gbAmount) {
                    return isset($product['amount'], $product['amount_unit'])
                        && strtoupper((string) $product['amount_unit']) === 'GB'
                        && (int) $product['amount'] === $gbAmount;
                })
                ->sortBy(function ($product) {
                    return [(float) ($product['price'] ?? 0), (int) ($product['duration'] ?? PHP_INT_MAX)];
                })
                ->first();

            if (!$selectedProduct) {
                // Fallback: try without country filter
                if ($countryCode) {
                    $allProducts = $esimService->getProducts([]);
                    $selectedProduct = collect($allProducts)
                        ->filter(function ($product) use ($gbAmount) {
                            return isset($product['amount'], $product['amount_unit'])
                                && strtoupper((string) $product['amount_unit']) === 'GB'
                                && (int) $product['amount'] === $gbAmount;
                        })
                        ->sortBy(function ($product) {
                            return [(float) ($product['price'] ?? 0), (int) ($product['duration'] ?? PHP_INT_MAX)];
                        })
                        ->first();
                }

                if (!$selectedProduct) {
                    return response()->json(['message' => "No product found for {$gbAmount} GB. Please try a different amount."], 422);
                }
            }

            $topupTransactionId = 'TOPUP-ADMIN-' . $transaction->id . '-' . time() . '-' . uniqid();

            $apiResponse = $esimService->createOrder(
                $selectedProduct['id'],
                $topupTransactionId,
                [
                    'operation_type' => 'TOPUP',
                    'iccid' => $transaction->iccid,
                ]
            );

            if (empty($apiResponse['id'])) {
                throw new \Exception('No valid order ID received from the API');
            }

            $esimService->activateOrder($apiResponse['id']);

            // Create a new transaction record for the recharge (purchase_amount = 0, admin-managed)
            Transaction::create([
                'order_id' => $apiResponse['id'],
                'transaction_id' => $topupTransactionId,
                'status' => $apiResponse['status'] ?? 'completed',
                'iccid' => $transaction->iccid,
                'esim_qr' => $transaction->esim_qr,
                'creation_time' => now(),
                'cliente_id' => $transaction->cliente_id,
                'beneficiario_id' => $transaction->beneficiario_id,
                'super_partner_id' => $transaction->super_partner_id,
                'plan_name' => $selectedProduct['name'] ?? $transaction->plan_name,
                'data_amount' => $gbAmount,
                'duration_days' => $selectedProduct['duration'] ?? null,
                'purchase_amount' => 0,
                'currency' => 'USD',
                'is_paid' => true,
                'paid_at' => now(),
            ]);

            // Send recharge notification email to the client
            if ($transaction->cliente && !empty($transaction->cliente->email)) {
                try {
                    Mail::to($transaction->cliente->email)->send(new EsimRechargeMail(
                        $transaction->cliente->email,
                        $gbAmount,
                        $transaction->iccid,
                        $selectedProduct['name'] ?? $transaction->plan_name
                    ));
                } catch (\Throwable $mailException) {
                    Log::error('Could not send eSIM recharge email.', [
                        'transaction_id' => $transaction->id,
                        'email' => $transaction->cliente->email,
                        'message' => $mailException->getMessage(),
                    ]);
                }
            }

            return response()->json([
                'message' => "eSIM recharged successfully with {$gbAmount} GB.",
            ]);
        } catch (\Exception $e) {
            Log::error('Error recharging eSIM: ' . $e->getMessage(), ['transaction_id' => $transaction->id]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletion of transactions is not allowed.
     *
     * @param Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        return response()->json(['message' => __('default.transactions_cannot_be_deleted')], 403);
    }
}
