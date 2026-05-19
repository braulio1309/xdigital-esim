<?php

namespace App\Console\Commands;

use App\Mail\App\Cliente\LowDataUsageMail;
use App\Models\App\Cliente\RechargeAccessToken;
use App\Models\App\Transaction\Transaction;
use App\Services\App\Cliente\RechargeAccessTokenService;
use App\Services\EsimFxService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyLowDataUsageCommand extends Command
{
    protected $signature = 'esim:notify-low-data';
    protected $description = 'Envía aviso de bajo consumo (~75%) con link tokenizado de recarga.';

    public function handle(EsimFxService $esimFxService, RechargeAccessTokenService $rechargeAccessTokenService): int
    {
        $candidates = Transaction::query()
            ->whereNotNull('order_id')
            ->whereNotNull('iccid')
            ->whereNotNull('cliente_id')
            ->where('status', 'completed')
            ->with('cliente')
            ->orderByDesc('creation_time')
            ->get()
            ->unique('iccid');

        $sent = 0;

        foreach ($candidates as $transaction) {
            if (!$transaction->cliente || empty($transaction->cliente->email)) {
                continue;
            }

            try {
                $detail = $esimFxService->getOrder($transaction->order_id);
                $subscription = $detail['subscription'] ?? [];
                $used = (float) ($subscription['used_amount'] ?? 0);
                $limit = (float) ($subscription['upper_limit_amount'] ?? ($subscription['uper_limit_amount'] ?? 0));

                if ($limit <= 0) {
                    continue;
                }

                $usagePercentage = round(($used / $limit) * 100, 2);

                if ($usagePercentage < 75) {
                    continue;
                }

                $alreadySent = RechargeAccessToken::query()
                    ->where('purpose', 'low_data_75')
                    ->where('transaction_id', $transaction->id)
                    ->where('created_at', '>=', now()->subDay())
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                $tokenPayload = $rechargeAccessTokenService->createForTransaction($transaction, [
                    'purpose' => 'low_data_75',
                    'expires_at' => now()->addDays(7),
                ]);

                Mail::to($transaction->cliente->email)->send(new LowDataUsageMail(
                    $transaction->cliente,
                    $transaction,
                    $tokenPayload['url'],
                    $usagePercentage
                ));

                $sent++;
            } catch (\Throwable $exception) {
                Log::warning('No fue posible procesar aviso de bajo consumo.', [
                    'transaction_id' => $transaction->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $this->info("Notificaciones enviadas: {$sent}");

        return self::SUCCESS;
    }
}
