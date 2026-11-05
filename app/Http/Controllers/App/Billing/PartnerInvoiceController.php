<?php

namespace App\Http\Controllers\App\Billing;

use App\Http\Controllers\Controller;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\PartnerInvoice;
use App\Models\App\Transaction\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PartnerInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => ['nullable', 'string', 'max:100'],
        ]);
        $invoiceNumber = trim($validated['invoice_number'] ?? '');
        $owner = $this->resolveOwner();
        $query = PartnerInvoice::query()->with('commissionNote')->orderByDesc('period_start')->orderByDesc('id');
        if ($invoiceNumber !== '') {
            $query->where('invoice_number', 'like', '%' . $invoiceNumber . '%');
        }
        $monthlyPartnerTotals = collect();
        $superPartnerInvoiceTotals = collect();
        $adminPartnerInvoiceTotals = collect();
        $months = collect();
        $superPartners = collect();
        $hasFilters = false;

        if ($owner['type'] === 'admin') {
            $request->validate([
                'month' => ['nullable', 'date_format:Y-m'],
                'super_partner_id' => ['nullable', 'integer', 'exists:super_partners,id'],
                'invoice_type' => ['nullable', 'in:super_partner,partner'],
                'invoice_number' => ['nullable', 'string', 'max:100'],
            ]);

            $periodStart = $request->filled('month')
                ? Carbon::createFromFormat('!Y-m', $request->input('month'))->toDateString()
                : null;
            $superPartnerId = $request->input('super_partner_id');
            $invoiceType = $request->input('invoice_type');
            $hasFilters = $request->filled('month')
                || $request->filled('super_partner_id')
                || $request->filled('invoice_type')
                || $invoiceNumber !== '';

            if ($hasFilters && (!$invoiceType || $invoiceType === 'super_partner')) {
                $superPartnerInvoiceTotals = PartnerInvoice::query()
                    ->where('partner_invoices.owner_type', 'super_partner')
                    ->when($invoiceNumber !== '', function ($builder) use ($invoiceNumber) {
                        $builder->where('partner_invoices.invoice_number', 'like', '%' . $invoiceNumber . '%');
                    })
                    ->when($superPartnerId, function ($builder) use ($superPartnerId) {
                        $builder->where('partner_invoices.owner_id', $superPartnerId);
                    })
                    ->when($periodStart, function ($builder) use ($periodStart) {
                        $builder->whereDate('partner_invoices.period_start', $periodStart);
                    })
                    ->selectRaw('period_start, owner_id as super_partner_id, COUNT(*) as invoice_count')
                    ->selectRaw('SUM(CASE WHEN amount > applied_commissions THEN amount - applied_commissions ELSE 0 END) as net_amount')
                    ->selectRaw('SUM(earned_commissions) as earned_commissions')
                    ->selectRaw('SUM(due_amount) as due_amount')
                    ->selectRaw('SUM(CASE WHEN is_paid = 1 THEN 1 ELSE 0 END) as paid_count')
                    ->groupBy('period_start', 'owner_id')
                    ->orderByDesc('period_start')
                    ->get();
            }

            if ($hasFilters && (!$invoiceType || $invoiceType === 'partner')) {
                $partnerInvoiceQuery = PartnerInvoice::query()
                    ->join('beneficiarios', 'beneficiarios.id', '=', 'partner_invoices.owner_id')
                    ->where('partner_invoices.owner_type', 'partner')
                    ->when($invoiceNumber !== '', function ($builder) use ($invoiceNumber) {
                        $builder->where('partner_invoices.invoice_number', 'like', '%' . $invoiceNumber . '%');
                    });
                if ($periodStart) {
                    $partnerInvoiceQuery->whereDate('partner_invoices.period_start', $periodStart);
                }
                if ($superPartnerId) {
                    $partnerInvoiceQuery->where('beneficiarios.super_partner_id', $superPartnerId);
                }

                $adminPartnerInvoiceTotals = $partnerInvoiceQuery
                    ->selectRaw('partner_invoices.period_start, beneficiarios.super_partner_id, COUNT(DISTINCT partner_invoices.owner_id) as partner_count')
                    ->selectRaw('COUNT(partner_invoices.id) as invoice_count')
                    ->selectRaw('SUM(CASE WHEN partner_invoices.amount > partner_invoices.applied_commissions THEN partner_invoices.amount - partner_invoices.applied_commissions ELSE 0 END) as net_amount')
                    ->selectRaw('SUM(partner_invoices.earned_commissions) as earned_commissions')
                    ->selectRaw('SUM(partner_invoices.due_amount) as due_amount')
                    ->selectRaw('SUM(CASE WHEN partner_invoices.is_paid = 1 THEN 1 ELSE 0 END) as paid_count')
                    ->groupBy('partner_invoices.period_start', 'beneficiarios.super_partner_id')
                    ->orderByDesc('partner_invoices.period_start')
                    ->get();
            }

            if ($hasFilters) {
                $query->when($invoiceType, function ($builder) use ($invoiceType) {
                    $builder->where('owner_type', $invoiceType);
                })->when($periodStart, function ($builder) use ($periodStart) {
                    $builder->whereDate('period_start', $periodStart);
                })->when($superPartnerId, function ($builder) use ($superPartnerId) {
                    $partnerIds = Beneficiario::query()
                        ->where('super_partner_id', $superPartnerId)
                        ->select('id');

                    $builder->where(function ($ownerQuery) use ($superPartnerId, $partnerIds) {
                        $ownerQuery->where(function ($superPartnerQuery) use ($superPartnerId) {
                            $superPartnerQuery->where('owner_type', 'super_partner')
                                ->where('owner_id', $superPartnerId);
                        })->orWhere(function ($partnerQuery) use ($partnerIds) {
                            $partnerQuery->where('owner_type', 'partner')
                                ->whereIn('owner_id', $partnerIds);
                        });
                    });
                });
            } else {
                $query->whereRaw('1 = 0');
            }

            $months = PartnerInvoice::query()
                ->select('period_start')
                ->distinct()
                ->orderByDesc('period_start')
                ->pluck('period_start')
                ->map(function ($periodStart) {
                    return Carbon::parse($periodStart)->format('Y-m');
                })
                ->unique()
                ->values();
            $superPartners = SuperPartner::query()->orderBy('nombre')->get(['id', 'nombre']);
        } elseif ($owner['type'] === 'partner') {
            $query->where('owner_type', 'partner')->where('owner_id', $owner['model']->id);
            $partnersById = collect([$owner['model']->id => $owner['model']]);
            $superPartnersById = collect();
        } else {
            $partnerIds = $owner['model']->beneficiarios()->pluck('id');
            $monthlyPartnerTotals = PartnerInvoice::query()
                ->where('owner_type', 'partner')
                ->whereIn('owner_id', $partnerIds)
                ->when($invoiceNumber !== '', function ($builder) use ($invoiceNumber) {
                    $builder->where('invoice_number', 'like', '%' . $invoiceNumber . '%');
                })
                ->selectRaw('period_start, COUNT(*) as partner_count')
                ->selectRaw('SUM(CASE WHEN amount > applied_commissions THEN amount - applied_commissions ELSE 0 END) as net_amount')
                ->selectRaw('SUM(earned_commissions) as earned_commissions')
                ->selectRaw('SUM(due_amount) as due_amount')
                ->groupBy('period_start')
                ->orderByDesc('period_start')
                ->get();
            $query->where(function ($builder) use ($owner, $partnerIds) {
                $builder->where(function ($direct) use ($owner) {
                    $direct->where('owner_type', 'super_partner')->where('owner_id', $owner['model']->id);
                })->orWhere(function ($partners) use ($partnerIds) {
                    $partners->where('owner_type', 'partner')->whereIn('owner_id', $partnerIds);
                });
            });
            $partnersById = Beneficiario::query()->whereIn('id', $partnerIds)->get()->keyBy('id');
            $superPartnersById = collect();
        }

        $invoices = $query->paginate(20)->withQueryString();
        if ($owner['type'] === 'admin') {
            $pageInvoices = $invoices->getCollection();
            $partnersById = Beneficiario::query()
                ->whereIn('id', $pageInvoices->where('owner_type', 'partner')->pluck('owner_id')->unique())
                ->get()
                ->keyBy('id');
            $superPartnersById = SuperPartner::query()
                ->whereIn('id', $pageInvoices->where('owner_type', 'super_partner')->pluck('owner_id')->unique())
                ->get()
                ->keyBy('id');

            $superPartnerNames = $superPartners->keyBy('id');
            $superPartnerInvoiceTotals->each(function ($total) use ($superPartnerNames) {
                $total->super_partner_name = optional($superPartnerNames->get($total->super_partner_id))->nombre;
            });
            $adminPartnerInvoiceTotals->each(function ($total) use ($superPartnerNames) {
                $total->super_partner_name = optional($superPartnerNames->get($total->super_partner_id))->nombre;
            });
        }

        $invoices->getCollection()->transform(function (PartnerInvoice $invoice) use ($owner, $partnersById, $superPartnersById) {
            $invoice->recipient_name = $invoice->owner_type === 'super_partner'
                ? ($owner['type'] === 'admin'
                    ? optional($superPartnersById->get($invoice->owner_id))->nombre
                    : $owner['model']->nombre)
                : optional($partnersById->get($invoice->owner_id))->nombre;

            return $invoice;
        });

        return view('partner-invoices.index', compact(
            'invoices',
            'owner',
            'invoiceNumber',
            'hasFilters',
            'monthlyPartnerTotals',
            'months',
            'superPartners',
            'superPartnerInvoiceTotals',
            'adminPartnerInvoiceTotals'
        ));
    }

    public function updatePaymentStatus(Request $request, PartnerInvoice $partnerInvoice)
    {
        $this->ensureAdministrator();

        $validated = $request->validate([
            'is_paid' => ['required', 'boolean'],
        ]);

        $partnerInvoice->update([
            'is_paid' => $validated['is_paid'],
        ]);

        return redirect()
            ->route('partner-invoices.index', $request->only('month', 'super_partner_id', 'invoice_type', 'invoice_number'))
            ->with('success', 'El estatus de pago de la factura se actualizó correctamente.');
    }

    public function download(PartnerInvoice $partnerInvoice)
    {
        $owner = $this->resolveOwner();

        if (!$this->canAccessInvoice($partnerInvoice, $owner)) {
            abort(404);
        }

        $partnerInvoice->loadMissing('commissionNote');

        $recipient = $partnerInvoice->owner_type === 'partner'
            ? Beneficiario::with('user')->findOrFail($partnerInvoice->owner_id)
            : SuperPartner::with('user')->findOrFail($partnerInvoice->owner_id);

        $logoPath = public_path('images/logo.png');
        $logoData = is_file($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        return app('dompdf.wrapper')->loadView('partner-invoices.pdf', [
            'invoice' => $partnerInvoice,
            'recipient' => $recipient,
            'logoData' => $logoData,
            'paymentDetails' => config('services.billing_payment'),
        ])->setPaper('a4')->download(strtolower($partnerInvoice->invoice_number) . '.pdf');
    }

    public function downloadMonthlyPartnerInvoice(Request $request, string $period)
    {
        $superPartner = $this->resolveMonthlyInvoiceSuperPartner($request);
        abort_unless(preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $period) === 1, 404);

        $periodStart = Carbon::createFromFormat('!Y-m', $period);
        $partnerIds = $superPartner->beneficiarios()->pluck('id');
        $invoices = PartnerInvoice::query()
            ->with('commissionNote')
            ->where('owner_type', 'partner')
            ->whereIn('owner_id', $partnerIds)
            ->whereDate('period_start', $periodStart->toDateString())
            ->orderBy('owner_id')
            ->get();

        $superPartnerInvoice = PartnerInvoice::query()
            ->with('commissionNote')
            ->where('owner_type', 'super_partner')
            ->where('owner_id', $superPartner->id)
            ->whereDate('period_start', $periodStart->toDateString())
            ->first();

        abort_if(
            $invoices->isEmpty() && (!$superPartnerInvoice || !$superPartnerInvoice->commissionNote),
            404
        );

        $partnersById = Beneficiario::query()
            ->whereIn('id', $invoices->pluck('owner_id')->unique())
            ->get()
            ->keyBy('id');

        $items = $invoices->flatMap(function (PartnerInvoice $invoice) use ($partnersById) {
            $partnerName = optional($partnersById->get($invoice->owner_id))->nombre ?: 'Partner no disponible';

            return collect($invoice->items ?? [])->map(function (array $item) use ($invoice, $partnerName) {
                return [
                    'partner_name' => $partnerName,
                    'invoice_number' => $invoice->invoice_number,
                    'plan_name' => $item['plan_name'] ?? 'eSIM',
                    'data_amount' => $item['data_amount'] ?? null,
                    'duration_days' => $item['duration_days'] ?? null,
                    'country_code' => $item['country_code'] ?? null,
                    'discount_percentage' => isset($item['discount_percentage']) ? (float) $item['discount_percentage'] : null,
                    'discount_region' => $item['discount_region'] ?? null,
                    'discount_unit_amount' => (float) ($item['discount_unit_amount'] ?? 0),
                    'discount_total' => (float) ($item['discount_total'] ?? 0),
                    'gross_unit_price' => (float) ($item['gross_unit_price'] ?? $item['unit_price'] ?? 0),
                    'quantity' => (int) ($item['quantity'] ?? 0),
                    'unit_price' => (float) ($item['unit_price'] ?? 0),
                    'total' => (float) ($item['total'] ?? 0),
                ];
            });
        })->values();

        $commissionRows = $this->buildMonthlySuperPartnerCommissionRows(
            $superPartner,
            $periodStart,
            $invoices,
            $superPartnerInvoice
        );
        $amount = round($invoices->sum('amount'), 2);
        $paidAmount = round($invoices->sum('paid_amount'), 2);
        $earnedCommissions = round($commissionRows->sum('total'), 2);
        $outstandingBeforeCommissions = round(max(0, $amount - $paidAmount), 2);
        $appliedCommissions = round(min($earnedCommissions, $outstandingBeforeCommissions), 2);
        $dueAmount = round(max(0, $outstandingBeforeCommissions - $appliedCommissions), 2);

        $logoPath = public_path('images/logo.png');
        $logoData = is_file($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        return app('dompdf.wrapper')->loadView('partner-invoices.monthly-consolidated-invoice', [
            'superPartner' => $superPartner,
            'periodStart' => $periodStart,
            'invoiceNumbers' => $invoices->pluck('invoice_number')->push(optional($superPartnerInvoice)->invoice_number)->filter()->values(),
            'items' => $items,
            'amount' => $amount,
            'paidAmount' => $paidAmount,
            'appliedCommissions' => $appliedCommissions,
            'earnedCommissions' => $earnedCommissions,
            'dueAmount' => $dueAmount,
            'currency' => optional($invoices->first() ?: $superPartnerInvoice)->currency ?: 'USD',
            'paymentDetails' => config('services.billing_payment'),
            'logoData' => $logoData,
        ])->setPaper('a4', 'landscape')->download('factura-consolidada-' . $period . '.pdf');
    }

    public function downloadCommissionNote(PartnerInvoice $partnerInvoice)
    {
        $owner = $this->resolveOwner();

        if (!$this->canAccessInvoice($partnerInvoice, $owner)) {
            abort(404);
        }

        $note = $partnerInvoice->commissionNote;
        abort_unless($note, 404);

        $recipient = $partnerInvoice->owner_type === 'partner'
            ? Beneficiario::with('user')->findOrFail($partnerInvoice->owner_id)
            : SuperPartner::with('user')->findOrFail($partnerInvoice->owner_id);

        $logoPath = public_path('images/logo.png');
        $logoData = is_file($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        return app('dompdf.wrapper')->loadView('partner-invoices.commission-note-pdf', [
            'invoice' => $partnerInvoice,
            'note' => $note,
            'recipient' => $recipient,
            'logoData' => $logoData,
        ])->setPaper('a4', 'landscape')->download(strtolower($note->note_number) . '.pdf');
    }

    public function downloadMonthlyCommissionReport(Request $request, string $period)
    {
        $superPartner = $this->resolveMonthlyInvoiceSuperPartner($request);
        abort_unless(preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $period) === 1, 404);

        $periodStart = Carbon::createFromFormat('!Y-m', $period);
        $partnerIds = $superPartner->beneficiarios()->pluck('id');
        $invoices = PartnerInvoice::query()
            ->with('commissionNote')
            ->where('owner_type', 'partner')
            ->whereIn('owner_id', $partnerIds)
            ->whereDate('period_start', $periodStart->toDateString())
            ->orderBy('owner_id')
            ->get();

        $superPartnerInvoice = PartnerInvoice::query()
            ->with('commissionNote')
            ->where('owner_type', 'super_partner')
            ->where('owner_id', $superPartner->id)
            ->whereDate('period_start', $periodStart->toDateString())
            ->first();

        $commissionRows = $this->buildMonthlySuperPartnerCommissionRows(
            $superPartner,
            $periodStart,
            $invoices,
            $superPartnerInvoice
        );

        abort_if($commissionRows->isEmpty(), 404);

        $earnedCommissions = round($commissionRows->sum('total'), 2);
        $appliedCommissions = round(min(
            $earnedCommissions,
            $invoices->sum(function (PartnerInvoice $invoice) {
                return optional($invoice->commissionNote)->applied_amount ?? 0;
            }) + (float) optional(optional($superPartnerInvoice)->commissionNote)->applied_amount
        ), 2);
        $balanceCommissions = round(max(0, $earnedCommissions - $appliedCommissions), 2);

        $logoPath = public_path('images/logo.png');
        $logoData = is_file($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        return app('dompdf.wrapper')->loadView('partner-invoices.monthly-commission-report', [
            'superPartner' => $superPartner,
            'periodStart' => $periodStart,
            'commissionRows' => $commissionRows,
            'earnedCommissions' => $earnedCommissions,
            'appliedCommissions' => $appliedCommissions,
            'balanceCommissions' => $balanceCommissions,
            'logoData' => $logoData,
        ])->setPaper('a4', 'landscape')->download('comisiones-partners-' . $period . '.pdf');
    }

    private function getCommissionRegionLabel(?string $countryCode): ?string
    {
        if (!$countryCode) {
            return null;
        }

        if (\App\Helpers\CountryTariffHelper::isLatamCountry($countryCode)) {
            return 'LATAM';
        }

        return \App\Helpers\CountryTariffHelper::isUsaCaEuCountry($countryCode)
            ? 'Europa y EE. UU.'
            : null;
    }

    private function buildMonthlySuperPartnerCommissionRows(
        SuperPartner $superPartner,
        Carbon $periodStart,
        \Illuminate\Support\Collection $partnerInvoices,
        ?PartnerInvoice $superPartnerInvoice
    ): \Illuminate\Support\Collection {
        $partnerIds = $superPartner->beneficiarios()->pluck('id');
        $transactions = Transaction::query()
            ->with(['beneficiario', 'superPartner', 'cliente.beneficiario'])
            ->whereBetween('creation_time', [
                $periodStart->copy()->startOfMonth()->startOfDay(),
                $periodStart->copy()->endOfMonth()->endOfDay(),
            ])
            ->where('purchase_amount', '>', 0)
            ->where(function ($query) use ($superPartner, $partnerIds) {
                $query->where(function ($direct) use ($superPartner) {
                    $direct->where('super_partner_id', $superPartner->id)
                        ->whereNull('beneficiario_id')
                        ->whereDoesntHave('cliente', function ($clienteQuery) {
                            $clienteQuery->whereNotNull('beneficiario_id');
                        });
                })->orWhereIn('beneficiario_id', $partnerIds)
                    ->orWhereHas('cliente', function ($clienteQuery) use ($partnerIds) {
                        $clienteQuery->whereIn('beneficiario_id', $partnerIds);
                    });
            })
            ->orderBy('creation_time')
            ->get();

        $partnerInvoicesByOwner = $partnerInvoices->keyBy('owner_id');

        return $transactions
            ->map(function (Transaction $transaction) use ($superPartner, $partnerInvoicesByOwner, $superPartnerInvoice) {
                $partner = $transaction->resolveBeneficiario();
                $isPartnerTransaction = $partner
                    && (int) $partner->super_partner_id === (int) $superPartner->id;
                $invoice = $isPartnerTransaction
                    ? $partnerInvoicesByOwner->get($partner->id)
                    : $superPartnerInvoice;
                $percentage = Transaction::getRegionalSaleCommissionPercentage(
                    $transaction->country_code,
                    $superPartner
                );
                $commission = $percentage !== null
                    ? round((float) $transaction->purchase_amount * $percentage / 100, 2)
                    : 0.0;

                if ($commission <= 0) {
                    return null;
                }

                return [
                    'partner_name' => $isPartnerTransaction
                        ? $partner->nombre
                        : 'Super Partner: ' . $superPartner->nombre,
                    'invoice_number' => optional($invoice)->invoice_number ?: 'N/D',
                    'note_number' => optional(optional($invoice)->commissionNote)->note_number ?: 'N/D',
                    'plan_name' => $transaction->plan_name ?: 'eSIM',
                    'data_amount' => $transaction->data_amount,
                    'duration_days' => $transaction->duration_days,
                    'country_code' => $transaction->country_code,
                    'commission_percentage' => $percentage,
                    'commission_region' => $this->getCommissionRegionLabel($transaction->country_code),
                    'quantity' => 1,
                    'unit_price' => $commission,
                    'total' => $commission,
                ];
            })
            ->filter()
            ->groupBy(function (array $row) {
                return implode('|', [
                    $row['partner_name'],
                    $row['invoice_number'],
                    $row['note_number'],
                    $row['plan_name'],
                    $row['data_amount'] ?? 'N/D',
                    $row['duration_days'] ?? 'N/D',
                    strtoupper((string) ($row['country_code'] ?? 'N/D')),
                    $row['commission_percentage'],
                ]);
            })
            ->map(function ($rows) {
                $row = $rows->first();
                $row['quantity'] = $rows->sum('quantity');
                $row['total'] = round($rows->sum('total'), 2);
                $row['unit_price'] = round($row['total'] / max($row['quantity'], 1), 2);

                return $row;
            })
            ->values();
    }

    private function resolveOwner(): array
    {
        $user = auth()->user();

        if ($user->user_type === 'admin') {
            return ['type' => 'admin', 'model' => null];
        }

        if ($user->user_type === 'beneficiario') {
            abort(403);
        }

        if ($user->user_type === 'admin_beneficiario' && $user->beneficiario_id) {
            abort(403);
        }

        if ($user->user_type === 'super_partner') {
            $superPartner = SuperPartner::where('user_id', $user->id)->first();
            abort_unless($superPartner, 403);

            return ['type' => 'super_partner', 'model' => $superPartner];
        }

        if ($user->user_type === 'admin_partner' && $user->super_partner_id) {
            return ['type' => 'super_partner', 'model' => SuperPartner::findOrFail($user->super_partner_id)];
        }

        abort(403);
    }

    private function canAccessInvoice(PartnerInvoice $invoice, array $owner): bool
    {
        if ($owner['type'] === 'admin') {
            return true;
        }

        if ($owner['type'] === 'partner') {
            return $invoice->owner_type === 'partner' && (int) $invoice->owner_id === (int) $owner['model']->id;
        }

        if ($invoice->owner_type === 'super_partner') {
            return (int) $invoice->owner_id === (int) $owner['model']->id;
        }

        return $invoice->owner_type === 'partner'
            && $owner['model']->beneficiarios()->whereKey($invoice->owner_id)->exists();
    }

    private function ensureAdministrator(): void
    {
        abort_unless(auth()->user()->user_type === 'admin', 403);
    }

    private function resolveMonthlyInvoiceSuperPartner(Request $request): SuperPartner
    {
        $owner = $this->resolveOwner();

        if ($owner['type'] === 'super_partner') {
            return $owner['model'];
        }

        abort_unless($owner['type'] === 'admin', 404);
        $validated = $request->validate([
            'super_partner_id' => ['required', 'integer', 'exists:super_partners,id'],
        ]);

        return SuperPartner::findOrFail($validated['super_partner_id']);
    }
}