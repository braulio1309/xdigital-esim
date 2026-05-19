<?php

namespace App\Services\App\Cliente;

use App\Models\App\Cliente\Cliente;
use App\Models\App\Cliente\RechargeAccessToken;
use App\Models\App\Transaction\Transaction;
use Illuminate\Support\Str;

class RechargeAccessTokenService
{
    public function createForTransaction(Transaction $transaction, array $options = []): array
    {
        $plainToken = Str::random(64);
        $expiresAt = $options['expires_at'] ?? now()->addHours(48);
        $purpose = $options['purpose'] ?? 'recharge';
        $transaction->loadMissing(['cliente', 'beneficiario']);
        $fallbackBeneficiarioId = $transaction->beneficiario_id ?: optional($transaction->cliente)->beneficiario_id;
        $fallbackSuperPartnerId = $transaction->super_partner_id
            ?: optional($transaction->beneficiario)->super_partner_id;

        $record = RechargeAccessToken::create([
            'cliente_id' => $transaction->cliente_id,
            'transaction_id' => $transaction->id,
            'beneficiario_id' => $options['beneficiario_id'] ?? $fallbackBeneficiarioId,
            'super_partner_id' => $options['super_partner_id'] ?? $fallbackSuperPartnerId,
            'country_code' => isset($options['country_code']) ? strtoupper((string) $options['country_code']) : $transaction->country_code,
            'token_hash' => hash('sha256', $plainToken),
            'purpose' => $purpose,
            'expires_at' => $expiresAt,
        ]);

        return [
            'token' => $plainToken,
            'record' => $record,
            'url' => route('recharge.token.access', ['token' => $plainToken]),
        ];
    }

    public function resolveValidToken(string $plainToken): ?RechargeAccessToken
    {
        return RechargeAccessToken::query()
            ->where('token_hash', hash('sha256', trim($plainToken)))
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->with(['transaction', 'cliente'])
            ->first();
    }

    public function setSessionContext(RechargeAccessToken $token): void
    {
        session([
            'planes_recharge_token_id' => $token->id,
            'planes_recharge_cliente_id' => $token->cliente_id,
        ]);

        if ($token->transaction && !empty($token->transaction->iccid)) {
            session([
                'planes_recharge_context' => [
                    'is_recharge' => true,
                    'iccid' => $token->transaction->iccid,
                    'transaction_id' => $token->transaction->transaction_id,
                ],
            ]);
        }

        if ($token->beneficiario_id || $token->super_partner_id) {
            session([
                'planes_partner_context' => [
                    'codigo' => null,
                    'beneficiario_id' => $token->beneficiario_id,
                    'super_partner_id' => $token->super_partner_id,
                ],
            ]);
        }
    }

    public function resolveSessionToken(): ?RechargeAccessToken
    {
        $tokenId = session('planes_recharge_token_id');

        if (!$tokenId) {
            return null;
        }

        return RechargeAccessToken::query()
            ->where('id', $tokenId)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->with(['transaction', 'cliente'])
            ->first();
    }

    public function resolveClienteFromSessionToken(): ?Cliente
    {
        $token = $this->resolveSessionToken();

        return $token ? $token->cliente : null;
    }

    public function consumeSessionToken(): void
    {
        $token = $this->resolveSessionToken();

        if ($token) {
            $token->forceFill(['used_at' => now()])->save();
        }

        session()->forget('planes_recharge_token_id');
        session()->forget('planes_recharge_cliente_id');
    }
}
