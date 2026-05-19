<?php

namespace App\Http\Controllers\App\Cliente;

use App\Http\Controllers\Controller;
use App\Models\App\Cliente\Cliente;
use App\Models\App\Transaction\Transaction;
use App\Services\App\Cliente\RechargeAccessTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicRechargeController extends Controller
{
    public function showDocumentForm()
    {
        return view('clientes.recarga-documento', [
            'cliente' => null,
            'transaction' => null,
            'rechargeUrl' => null,
        ]);
    }

    public function lookupByDocument(Request $request, RechargeAccessTokenService $tokenService)
    {
        $request->validate([
            'identificador' => 'required|string|max:255',
        ]);

        $identificador = trim((string) $request->input('identificador'));
        $cliente = Cliente::query()
            ->where('identificador', $identificador)
            ->first();

        if (!$cliente) {
            return back()->withErrors(['identificador' => 'No encontramos un cliente con ese documento.'])->withInput();
        }

        $transaction = Transaction::query()
            ->where('cliente_id', $cliente->id)
            ->whereNotNull('order_id')
            ->whereNotNull('iccid')
            ->orderByDesc('creation_time')
            ->first();

        if (!$transaction) {
            return back()->withErrors(['identificador' => 'El cliente no tiene una eSIM activa para recargar.'])->withInput();
        }

        $tokenPayload = $tokenService->createForTransaction($transaction, [
            'purpose' => 'document_recharge',
            'expires_at' => now()->addHours(2),
        ]);

        return view('clientes.recarga-documento', [
            'cliente' => $cliente,
            'transaction' => $transaction,
            'rechargeUrl' => $tokenPayload['url'],
        ]);
    }

    public function accessWithToken(string $token, RechargeAccessTokenService $tokenService)
    {
        $tokenRecord = $tokenService->resolveValidToken($token);

        if (!$tokenRecord || !$tokenRecord->transaction || empty($tokenRecord->transaction->iccid)) {
            return redirect()->route('recharge.document.form')
                ->with('error', 'El enlace de recarga es inválido o expiró.');
        }

        $tokenService->setSessionContext($tokenRecord);

        $transaction = $tokenRecord->transaction;
        $country = $transaction->country_code ? strtoupper((string) $transaction->country_code) : null;

        $referralCode = null;

        if ($transaction->beneficiario) {
            $referralCode = Str::slug($transaction->beneficiario->nombre) . '-' . $transaction->beneficiario->codigo;
        } elseif ($transaction->superPartner) {
            $referralCode = Str::slug($transaction->superPartner->nombre) . '-' . $transaction->superPartner->codigo;
        }

        $routeParams = [];

        if ($referralCode) {
            $routeParams['referralCode'] = $referralCode;
        }

        if ($country && strlen($country) === 2) {
            $routeParams['country'] = $country;
        }

        $routeParams['recharge_iccid'] = $transaction->iccid;

        return redirect()->route('planes.index', $routeParams);
    }
}
