<?php

namespace App\Http\Controllers\App\Billing;

use App\Http\Controllers\Controller;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\PartnerInvoice;
use Illuminate\Http\Request;

class PartnerInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $owner = $this->resolveOwner();
        $query = PartnerInvoice::query()->with('commissionNote')->orderByDesc('period_start')->orderByDesc('id');

        if ($owner['type'] === 'partner') {
            $query->where('owner_type', 'partner')->where('owner_id', $owner['model']->id);
            $partnersById = collect([$owner['model']->id => $owner['model']]);
        } else {
            $partnerIds = $owner['model']->beneficiarios()->pluck('id');
            $query->where(function ($builder) use ($owner, $partnerIds) {
                $builder->where(function ($direct) use ($owner) {
                    $direct->where('owner_type', 'super_partner')->where('owner_id', $owner['model']->id);
                })->orWhere(function ($partners) use ($partnerIds) {
                    $partners->where('owner_type', 'partner')->whereIn('owner_id', $partnerIds);
                });
            });
            $partnersById = Beneficiario::query()->whereIn('id', $partnerIds)->get()->keyBy('id');
        }

        $invoices = $query->paginate(20)->withQueryString();
        $invoices->getCollection()->transform(function (PartnerInvoice $invoice) use ($owner, $partnersById) {
            $invoice->recipient_name = $invoice->owner_type === 'super_partner'
                ? $owner['model']->nombre
                : optional($partnersById->get($invoice->owner_id))->nombre;

            return $invoice;
        });

        return view('partner-invoices.index', compact('invoices', 'owner'));
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
        ])->setPaper('a4')->download(strtolower($partnerInvoice->invoice_number) . '.pdf');
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

    private function resolveOwner(): array
    {
        $user = auth()->user();

        if ($user->user_type === 'beneficiario') {
            $partner = Beneficiario::where('user_id', $user->id)->first();
            abort_unless($partner, 403);

            return ['type' => 'partner', 'model' => $partner];
        }

        if ($user->user_type === 'admin_beneficiario' && $user->beneficiario_id) {
            return ['type' => 'partner', 'model' => Beneficiario::findOrFail($user->beneficiario_id)];
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
        if ($owner['type'] === 'partner') {
            return $invoice->owner_type === 'partner' && (int) $invoice->owner_id === (int) $owner['model']->id;
        }

        if ($invoice->owner_type === 'super_partner') {
            return (int) $invoice->owner_id === (int) $owner['model']->id;
        }

        return $invoice->owner_type === 'partner'
            && $owner['model']->beneficiarios()->whereKey($invoice->owner_id)->exists();
    }
}