<?php

namespace App\Services\App\Billing;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Transaction\PartnerCommissionNote;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\PartnerInvoice;
use App\Models\App\Transaction\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PartnerInvoiceService
{
    public function generateForPeriod(Carbon $periodStart, Carbon $periodEnd, bool $refreshExisting = false): int
    {
        $periodStart = $periodStart->copy()->startOfDay();
        $periodEnd = $periodEnd->copy()->endOfDay();
        $generated = 0;

        Beneficiario::query()->each(function (Beneficiario $partner) use ($periodStart, $periodEnd, $refreshExisting, &$generated) {
            $generated += $this->generateInvoice('partner', $partner->id, $periodStart, $periodEnd, $refreshExisting) ? 1 : 0;
        });

        SuperPartner::query()->each(function (SuperPartner $superPartner) use ($periodStart, $periodEnd, $refreshExisting, &$generated) {
            $generated += $this->generateInvoice('super_partner', $superPartner->id, $periodStart, $periodEnd, $refreshExisting) ? 1 : 0;
        });

        return $generated;
    }

    private function generateInvoice(string $ownerType, int $ownerId, Carbon $periodStart, Carbon $periodEnd, bool $refreshExisting): bool
    {
        $existingInvoice = PartnerInvoice::query()
            ->where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->whereDate('period_start', $periodStart->toDateString())
            ->first();

        if ($existingInvoice && !$refreshExisting) {
            return false;
        }

        $transactions = $this->billableTransactions($ownerType, $ownerId, $periodStart, $periodEnd);
        $items = $this->buildItems($transactions);
        $amount = round(array_sum(array_column($items, 'total')), 2);
        $paidAmount = round(array_sum(array_column($items, 'paid_total')), 2);
        $commissionTransactions = $this->commissionTransactions($ownerType, $ownerId, $periodStart, $periodEnd);
        $commissionItems = $this->buildCommissionItems($commissionTransactions, $ownerType);
        $earnedCommissions = round(array_sum(array_column($commissionItems, 'total')), 2);
        $outstandingBeforeCommissions = round(max(0, $amount - $paidAmount), 2);
        $appliedCommissions = round(min($earnedCommissions, $outstandingBeforeCommissions), 2);
        $dueAmount = round(max(0, $outstandingBeforeCommissions - $appliedCommissions), 2);

        if ($amount <= 0 && $earnedCommissions <= 0) {
            return false;
        }

        DB::transaction(function () use ($existingInvoice, $ownerType, $ownerId, $periodStart, $periodEnd, $transactions, $items, $amount, $paidAmount, $earnedCommissions, $appliedCommissions, $dueAmount, $commissionItems) {
            $invoiceData = [
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'amount' => $amount,
                'paid_amount' => $paidAmount,
                'earned_commissions' => $earnedCommissions,
                'applied_commissions' => $appliedCommissions,
                'due_amount' => $dueAmount,
                'currency' => 'USD',
                'items' => $items,
                'transactions_count' => $transactions->count(),
            ];

            if ($existingInvoice) {
                $invoice = $existingInvoice;
                $invoice->update($invoiceData);
            } else {
                $invoice = PartnerInvoice::create(array_merge($invoiceData, [
                    'owner_type' => $ownerType,
                    'owner_id' => $ownerId,
                    'invoice_number' => 'PENDING-' . strtoupper(Str::random(20)),
                    'issued_at' => now(),
                ]));

                $prefix = $ownerType === 'partner' ? 'XC-P' : 'XC-SP';
                $invoice->update([
                    'invoice_number' => sprintf('%s-%s-%06d', $prefix, $periodStart->format('Ym'), $invoice->id),
                ]);
            }

            if ($earnedCommissions > 0) {
                $notePrefix = $ownerType === 'partner' ? 'XC-NC-P' : 'XC-NC-SP';
                $commissionNote = PartnerCommissionNote::firstOrNew([
                    'partner_invoice_id' => $invoice->id,
                ]);
                $noteData = [
                    'amount' => $earnedCommissions,
                    'applied_amount' => $appliedCommissions,
                    'balance_amount' => round($earnedCommissions - $appliedCommissions, 2),
                    'items' => $commissionItems,
                ];

                if (!$commissionNote->exists) {
                    $commissionNote->fill(array_merge($noteData, [
                        'note_number' => 'PENDING-' . strtoupper(Str::random(20)),
                        'issued_at' => now(),
                    ]));
                    $commissionNote->save();
                    $commissionNote->update([
                        'note_number' => sprintf('%s-%s-%06d', $notePrefix, $periodStart->format('Ym'), $commissionNote->id),
                    ]);
                } else {
                    $commissionNote->update($noteData);
                }
            } elseif ($existingInvoice) {
                $existingInvoice->commissionNote()->delete();
            }
        });

        return $existingInvoice === null || $refreshExisting;
    }

    private function billableTransactions(string $ownerType, int $ownerId, Carbon $periodStart, Carbon $periodEnd): Collection
    {
        $query = Transaction::query()
            ->with(['cliente.beneficiario'])
            ->whereBetween('creation_time', [$periodStart, $periodEnd])
            ->where('purchase_amount', 0);

        if ($ownerType === 'partner') {
            $query->where(function ($builder) use ($ownerId) {
                $builder->where('beneficiario_id', $ownerId)
                    ->orWhere(function ($legacy) use ($ownerId) {
                        $legacy->whereNull('beneficiario_id')
                            ->whereHas('cliente', function ($clienteQuery) use ($ownerId) {
                                $clienteQuery->where('beneficiario_id', $ownerId);
                            });
                    });
            });
        } else {
            $query->where('super_partner_id', $ownerId)
                ->whereNull('beneficiario_id')
                ->whereDoesntHave('cliente', function ($clienteQuery) {
                    $clienteQuery->whereNotNull('beneficiario_id');
                });
        }

        return $query->orderBy('creation_time')->get();
    }

    private function commissionTransactions(string $ownerType, int $ownerId, Carbon $periodStart, Carbon $periodEnd): Collection
    {
        $query = Transaction::query()
            ->with(['cliente.beneficiario'])
            ->whereBetween('creation_time', [$periodStart, $periodEnd])
            ->where('purchase_amount', '>', 0);

        if ($ownerType === 'partner') {
            $query->where(function ($builder) use ($ownerId) {
                $builder->where('beneficiario_id', $ownerId)
                    ->orWhere(function ($legacy) use ($ownerId) {
                        $legacy->whereNull('beneficiario_id')
                            ->whereHas('cliente', function ($clienteQuery) use ($ownerId) {
                                $clienteQuery->where('beneficiario_id', $ownerId);
                            });
                    });
            });
        } else {
            $partnerIds = SuperPartner::findOrFail($ownerId)->beneficiarios()->pluck('id');
            $query->where(function ($builder) use ($ownerId, $partnerIds) {
                $builder->where('super_partner_id', $ownerId)
                    ->orWhereIn('beneficiario_id', $partnerIds)
                    ->orWhereHas('cliente', function ($clienteQuery) use ($partnerIds) {
                        $clienteQuery->whereIn('beneficiario_id', $partnerIds);
                    });
            });
        }

        return $query->orderBy('creation_time')->get();
    }

    private function buildItems(Collection $transactions): array
    {
        return $transactions
            ->groupBy(function (Transaction $transaction) {
                $discountPercentage = $this->getFreePlanDiscountPercentage($transaction);

                return implode('|', [
                    $transaction->plan_name ?: 'eSIM',
                    $transaction->data_amount ?: 'N/D',
                    $transaction->duration_days ?: 'N/D',
                    strtoupper((string) ($transaction->country_code ?: 'N/D')),
                    $discountPercentage ?? 'none',
                ]);
            })
            ->map(function (Collection $planTransactions) {
                $first = $planTransactions->first();
                $discountPercentage = $this->getFreePlanDiscountPercentage($first);
                $grossTotal = 0.0;
                $total = 0.0;

                foreach ($planTransactions as $transaction) {
                    $amounts = $this->getFreePlanAmounts($transaction, $discountPercentage);
                    $grossTotal += $amounts['gross'];
                    $total += $amounts['net'];
                }

                $grossTotal = round($grossTotal, 2);
                $total = round($total, 2);
                $quantity = max($planTransactions->count(), 1);

                $paidTotal = round($planTransactions->filter(function (Transaction $transaction) {
                    return (bool) $transaction->is_paid;
                })->sum(function (Transaction $transaction) use ($discountPercentage) {
                    return $this->getFreePlanAmounts($transaction, $discountPercentage)['net'];
                }), 2);

                return [
                    'plan_name' => $first->plan_name ?: 'eSIM',
                    'data_amount' => $first->data_amount,
                    'duration_days' => $first->duration_days,
                    'country_code' => $first->country_code,
                    'discount_percentage' => $discountPercentage,
                    'discount_region' => $discountPercentage === 15.0 ? 'LATAM' : ($discountPercentage !== null ? 'Europa y EE. UU.' : null),
                    'discount_unit_amount' => round(($grossTotal - $total) / $quantity, 2),
                    'discount_total' => round($grossTotal - $total, 2),
                    'gross_unit_price' => round($grossTotal / $quantity, 2),
                    'quantity' => $planTransactions->count(),
                    'unit_price' => round($total / $quantity, 2),
                    'total' => $total,
                    'paid_total' => $paidTotal,
                    'due_total' => round(max(0, $total - $paidTotal), 2),
                ];
            })
            ->values()
            ->all();
    }

    private function buildCommissionItems(Collection $transactions, string $ownerType): array
    {
        $commissionColumn = $ownerType === 'partner'
            ? 'partner_sale_commission_amount'
            : 'super_partner_sale_commission_amount';

        return $transactions
            ->filter(function (Transaction $transaction) use ($commissionColumn) {
                return (float) $transaction->{$commissionColumn} > 0;
            })
            ->groupBy(function (Transaction $transaction) {
                $regionalPercentage = Transaction::getLargePlanRegionalPercentage(
                    $transaction->country_code,
                    $transaction->data_amount !== null ? (float) $transaction->data_amount : null
                );

                return implode('|', [
                    $transaction->plan_name ?: 'eSIM',
                    $transaction->data_amount ?: 'N/D',
                    $transaction->duration_days ?: 'N/D',
                    strtoupper((string) ($transaction->country_code ?: 'N/D')),
                    $regionalPercentage ?? 'configured',
                ]);
            })
            ->map(function (Collection $planTransactions) use ($commissionColumn) {
                $first = $planTransactions->first();
                $total = round($planTransactions->sum(function (Transaction $transaction) use ($commissionColumn) {
                    return (float) $transaction->{$commissionColumn};
                }), 2);
                $purchaseTotal = round($planTransactions->sum(function (Transaction $transaction) {
                    return (float) $transaction->purchase_amount;
                }), 2);
                $regionalPercentage = Transaction::getLargePlanRegionalPercentage(
                    $first->country_code,
                    $first->data_amount !== null ? (float) $first->data_amount : null
                );

                return [
                    'plan_name' => $first->plan_name ?: 'eSIM',
                    'data_amount' => $first->data_amount,
                    'duration_days' => $first->duration_days,
                    'country_code' => $first->country_code,
                    'commission_percentage' => $regionalPercentage !== null && $commissionColumn === 'super_partner_sale_commission_amount'
                        ? $regionalPercentage
                        : ($purchaseTotal > 0 ? round($total / $purchaseTotal * 100, 2) : null),
                    'commission_region' => $regionalPercentage !== null && $commissionColumn === 'super_partner_sale_commission_amount'
                        ? ($regionalPercentage === 15.0 ? 'LATAM' : 'Europa y EE. UU.')
                        : null,
                    'quantity' => $planTransactions->count(),
                    'unit_price' => round($total / max($planTransactions->count(), 1), 2),
                    'total' => $total,
                ];
            })
            ->values()
            ->all();
    }

    private function getFreePlanDiscountPercentage(Transaction $transaction): ?float
    {
        if ((float) $transaction->purchase_amount > 0) {
            return null;
        }

        return Transaction::getLargePlanRegionalPercentage(
            $transaction->country_code,
            $transaction->data_amount !== null ? (float) $transaction->data_amount : null
        );
    }

    private function getFreePlanAmounts(Transaction $transaction, ?float $discountPercentage): array
    {
        $commissionAmount = round($transaction->getCommissionAmount(), 2);

        if ($discountPercentage === null) {
            return ['gross' => $commissionAmount, 'net' => $commissionAmount];
        }

        $referenceAmount = round((float) $transaction->reference_purchase_amount, 2);
        if ($referenceAmount <= 0) {
            return [
                'gross' => round($commissionAmount / (1 - $discountPercentage / 100), 2),
                'net' => $commissionAmount,
            ];
        }

        return [
            'gross' => $referenceAmount,
            'net' => $referenceAmount > $commissionAmount
                ? $commissionAmount
                : round($referenceAmount * (1 - $discountPercentage / 100), 2),
        ];
    }
}