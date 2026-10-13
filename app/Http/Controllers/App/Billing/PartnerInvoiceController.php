<?php

namespace App\Http\Controllers\App\Billing;

use App\Http\Controllers\Controller;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\PartnerInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PartnerInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $owner = $this->resolveOwner();
        $query = PartnerInvoice::query()->with('commissionNote')->orderByDesc('period_start')->orderByDesc('id');
        $monthlyPartnerTotals = collect();
        $months = collect();

        if ($owner['type'] === 'admin') {
            $request->validate([
                'month' => ['nullable', 'date_format:Y-m'],
            ]);

            if ($request->filled('month')) {
                $periodStart = Carbon::createFromFormat('!Y-m', $request->input('month'));
                $query->whereDate('period_start', $periodStart->toDateString());
            }

            $months = PartnerInvoice::query()
                ->select('period_start')
                ->distinct()
                ->orderByDesc('period_start')
                ->pluck('period_start')
                ->map(function ($periodStart) {
                    return Carbon::parse($periodStart)->format('Y-m');
                });
        } elseif ($owner['type'] === 'partner') {
            $query->where('owner_type', 'partner')->where('owner_id', $owner['model']->id);
            $partnersById = collect([$owner['model']->id => $owner['model']]);
            $superPartnersById = collect();
        } else {
            $partnerIds = $owner['model']->beneficiarios()->pluck('id');
            $monthlyPartnerTotals = PartnerInvoice::query()
                ->where('owner_type', 'partner')
                ->whereIn('owner_id', $partnerIds)
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
        }

        $invoices->getCollection()->transform(function (PartnerInvoice $invoice) use ($owner, $partnersById, $superPartnersById) {
            $invoice->recipient_name = $invoice->owner_type === 'super_partner'
                ? ($owner['type'] === 'admin'
                    ? optional($superPartnersById->get($invoice->owner_id))->nombre
                    : $owner['model']->nombre)
                : optional($partnersById->get($invoice->owner_id))->nombre;

            return $invoice;
        });

        return view('partner-invoices.index', compact('invoices', 'owner', 'monthlyPartnerTotals', 'months'));
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
            ->route('partner-invoices.index', $request->only('month'))
            ->with('success', 'El estatus de pago de la factura se actualizó correctamente.');
    }

    public function download(PartnerInvoice $partnerInvoice)
    {
        $owner = $this->resolveOwner();

        if (!$this->canAccessInvoice($partnerInvoice, $owner)) {
            abort(404);
        }

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

    public function downloadMonthlyPartnerInvoice(string $period)
    {
        $owner = $this->resolveOwner();
        abort_unless($owner['type'] === 'super_partner', 404);
        abort_unless(preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $period) === 1, 404);

        $periodStart = Carbon::createFromFormat('!Y-m', $period);
        $partnerIds = $owner['model']->beneficiarios()->pluck('id');
        $invoices = PartnerInvoice::query()
            ->where('owner_type', 'partner')
            ->whereIn('owner_id', $partnerIds)
            ->whereDate('period_start', $periodStart->toDateString())
            ->orderBy('owner_id')
            ->get();

        abort_if($invoices->isEmpty(), 404);

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
                    'quantity' => (int) ($item['quantity'] ?? 0),
                    'unit_price' => (float) ($item['unit_price'] ?? 0),
                    'total' => (float) ($item['total'] ?? 0),
                ];
            });
        })->values();

        $logoPath = public_path('images/logo.png');
        $logoData = is_file($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        return app('dompdf.wrapper')->loadView('partner-invoices.monthly-consolidated-invoice', [
            'superPartner' => $owner['model'],
            'periodStart' => $periodStart,
            'invoiceNumbers' => $invoices->pluck('invoice_number'),
            'items' => $items,
            'amount' => round($invoices->sum('amount'), 2),
            'paidAmount' => round($invoices->sum('paid_amount'), 2),
            'appliedCommissions' => round($invoices->sum('applied_commissions'), 2),
            'earnedCommissions' => round($invoices->sum('earned_commissions'), 2),
            'dueAmount' => round($invoices->sum('due_amount'), 2),
            'currency' => $invoices->first()->currency,
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
        ])->setPaper('a4')->download(strtolower($note->note_number) . '.pdf');
    }

    public function downloadMonthlyCommissionReport(string $period)
    {
        $owner = $this->resolveOwner();
        abort_unless($owner['type'] === 'super_partner', 404);
        abort_unless(preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $period) === 1, 404);

        $periodStart = Carbon::createFromFormat('!Y-m', $period);
        $partnerIds = $owner['model']->beneficiarios()->pluck('id');
        $invoices = PartnerInvoice::query()
            ->with('commissionNote')
            ->where('owner_type', 'partner')
            ->whereIn('owner_id', $partnerIds)
            ->whereDate('period_start', $periodStart->toDateString())
            ->whereHas('commissionNote', function ($query) {
                $query->where('amount', '>', 0);
            })
            ->orderBy('owner_id')
            ->get();

        abort_if($invoices->isEmpty(), 404);

        $partnersById = Beneficiario::query()
            ->whereIn('id', $invoices->pluck('owner_id')->unique())
            ->get()
            ->keyBy('id');

        $commissionRows = $invoices->flatMap(function (PartnerInvoice $invoice) use ($partnersById) {
            $partner = $partnersById->get($invoice->owner_id);

            return collect($invoice->commissionNote->items ?? [])
                ->filter(function (array $item) {
                    return (float) ($item['total'] ?? 0) > 0;
                })
                ->map(function (array $item) use ($invoice, $partner) {
                    return [
                        'partner_name' => $partner ? $partner->nombre : 'Partner no disponible',
                        'invoice_number' => $invoice->invoice_number,
                        'note_number' => $invoice->commissionNote->note_number,
                        'plan_name' => $item['plan_name'] ?? 'eSIM',
                        'data_amount' => $item['data_amount'] ?? null,
                        'duration_days' => $item['duration_days'] ?? null,
                        'quantity' => (int) ($item['quantity'] ?? 0),
                        'unit_price' => (float) ($item['unit_price'] ?? 0),
                        'total' => (float) ($item['total'] ?? 0),
                    ];
                });
        })->values();

        $logoPath = public_path('images/logo.png');
        $logoData = is_file($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        return app('dompdf.wrapper')->loadView('partner-invoices.monthly-commission-report', [
            'superPartner' => $owner['model'],
            'periodStart' => $periodStart,
            'commissionRows' => $commissionRows,
            'earnedCommissions' => round($invoices->sum(function (PartnerInvoice $invoice) {
                return (float) $invoice->commissionNote->amount;
            }), 2),
            'appliedCommissions' => round($invoices->sum(function (PartnerInvoice $invoice) {
                return (float) $invoice->commissionNote->applied_amount;
            }), 2),
            'balanceCommissions' => round($invoices->sum(function (PartnerInvoice $invoice) {
                return (float) $invoice->commissionNote->balance_amount;
            }), 2),
            'logoData' => $logoData,
        ])->setPaper('a4', 'landscape')->download('comisiones-partners-' . $period . '.pdf');
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
}