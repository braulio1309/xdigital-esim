<?php

namespace App\Services\App\Cliente;

use App\Mail\App\Cliente\FreeEsimInvitationMail;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Cliente\Cliente;
use App\Models\App\SuperPartner\SuperPartner;
use Illuminate\Support\Facades\Mail;

class FreeEsimInvitationMailService
{
    public function send(Cliente $cliente, ?Beneficiario $beneficiario = null, ?SuperPartner $superPartner = null): void
    {
        if (empty($cliente->email)) {
            return;
        }

        $targetBeneficiario = $beneficiario;

        if (!$targetBeneficiario && $cliente->beneficiario) {
            $targetBeneficiario = $cliente->beneficiario;
        }

        if (!$targetBeneficiario) {
            $targetBeneficiario = $cliente->partners()->orderBy('cliente_beneficiario.id')->first();
        }

        $targetSuperPartner = $superPartner;

        if (!$targetSuperPartner && $targetBeneficiario && $targetBeneficiario->super_partner_id) {
            $targetSuperPartner = SuperPartner::find($targetBeneficiario->super_partner_id);
        }

        if (!$targetSuperPartner && $cliente->user && $cliente->user->super_partner_id) {
            $targetSuperPartner = SuperPartner::find($cliente->user->super_partner_id);
        }

        $activationUrl = $targetBeneficiario
            ? $targetBeneficiario->referral_link
            : ($targetSuperPartner ? $targetSuperPartner->referral_link : route('registro.esim.form'));

        $partnerName = $targetBeneficiario->nombre ?? ($targetSuperPartner->nombre ?? null);

        Mail::to($cliente->email)->send(new FreeEsimInvitationMail($cliente, $activationUrl, $partnerName));
    }
}
