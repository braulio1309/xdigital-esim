<?php

namespace App\Console\Commands;

use App\Mail\App\Cliente\LowDataUsageMail;
use App\Models\App\Transaction\Transaction;
use App\Services\App\Cliente\RechargeAccessTokenService;
use App\Services\EsimFxService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyLowDataUsageCommand extends Command
{
    protected $signature = 'notificar:consumo-esim';
    protected $aliases = ['esim:notify-low-data'];
    protected $description = 'Envía avisos de consumo eSIM (75% y 90%) con link tokenizado de recarga.';

    public function handle(EsimFxService $esimFxService, RechargeAccessTokenService $rechargeAccessTokenService): int
    {
        $candidates = Transaction::query()
            ->whereNotNull('order_id')
            ->whereNotNull('iccid')
            ->whereNotNull('cliente_id')
            ->where('status', 'completed')
            ->where(function ($query) {
                $query->whereNull('usage_75_notified_at')
                    ->orWhereNull('usage_90_notified_at');
            })
            ->whereHas('cliente', function ($query) {
                $query->whereNotNull('email');
            })
            ->whereHas('cliente.user', function ($query) {
                $query->active();
            })
            ->with(['cliente', 'beneficiario'])
            ->orderBy('id')
            ->get();

        $sent75 = 0;
        $sent90 = 0;

        foreach ($candidates as $transaction) {
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

                if ($this->dispatchThresholdNotification($transaction, 75, $usagePercentage, $rechargeAccessTokenService)) {
                    $sent75++;
                }

                if ($usagePercentage >= 90 && $this->dispatchThresholdNotification($transaction, 90, $usagePercentage, $rechargeAccessTokenService)) {
                    $sent90++;
                }
            } catch (\Throwable $exception) {
                Log::warning('No fue posible procesar aviso de bajo consumo.', [
                    'transaction_id' => $transaction->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $this->info("Notificaciones enviadas: 75%={$sent75}, 90%={$sent90}");

        return self::SUCCESS;
    }

    protected function dispatchThresholdNotification(
        Transaction $transaction,
        int $threshold,
        float $usagePercentage,
        RechargeAccessTokenService $rechargeAccessTokenService
    ): bool {
        $field = $threshold === 90 ? 'usage_90_notified_at' : 'usage_75_notified_at';

        return DB::transaction(function () use ($transaction, $threshold, $usagePercentage, $rechargeAccessTokenService, $field) {
            $lockedTransaction = Transaction::query()
                ->with('cliente')
                ->lockForUpdate()
                ->find($transaction->id);

            if (!$lockedTransaction || $lockedTransaction->{$field}) {
                return false;
            }

            if (!$lockedTransaction->cliente || empty($lockedTransaction->cliente->email)) {
                return false;
            }

            $tokenPayload = $rechargeAccessTokenService->createForTransaction($lockedTransaction, [
                'purpose' => sprintf('low_data_%d', $threshold),
                'expires_at' => now()->addDays(7),
            ]);

            Mail::to($lockedTransaction->cliente->email)->queue(new LowDataUsageMail(
                $lockedTransaction->cliente,
                $lockedTransaction,
                $tokenPayload['url'],
                $usagePercentage,
                $threshold
            ));

            $lockedTransaction->forceFill([
                $field => now(),
            ])->save();

            return true;
        }, 3);
    }
}
