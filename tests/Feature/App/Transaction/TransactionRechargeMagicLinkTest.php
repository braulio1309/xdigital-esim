<?php

namespace Tests\Feature\App\Transaction;

use App\Models\App\Cliente\Cliente;
use App\Models\App\Transaction\RechargeEmailToken;
use App\Models\App\Transaction\Transaction;
use App\Models\Core\Status;
use App\Models\Core\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransactionRechargeMagicLinkTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_redirects_recharge_email_links_with_the_transaction_country_preselected(): void
    {
        $statusId = $this->ensureActiveUserStatus();

        $user = User::create([
            'first_name' => 'Cliente',
            'last_name' => 'Demo',
            'email' => 'cliente-user@example.com',
            'password' => Hash::make('password123'),
            'status_id' => $statusId,
            'user_type' => 'cliente',
        ]);

        $cliente = Cliente::create([
            'nombre' => 'Cliente',
            'apellido' => 'Demo',
            'email' => 'cliente@example.com',
            'identificador' => 'ABC123',
            'user_id' => $user->id,
            'can_activate_free_esim' => true,
            'free_esim_capacity' => 1,
        ]);

        $transaction = Transaction::create([
            'transaction_id' => 'WEB-RECHARGE-1',
            'status' => 'completed',
            'iccid' => 'ICCID-123456789',
            'creation_time' => now(),
            'cliente_id' => $cliente->id,
            'order_id' => 'ORDER-123',
            'plan_name' => 'Plan 3GB',
            'data_amount' => 3,
            'duration_days' => 30,
            'purchase_amount' => 10,
            'country_code' => 'co',
        ]);

        $plainToken = Str::random(64);

        RechargeEmailToken::create([
            'transaction_id' => $transaction->id,
            'cliente_id' => $cliente->id,
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->get(route('transactions.recharge-magic-link', ['token' => $plainToken]));

        $response->assertRedirect(url('/planes-disponibles?recharge_iccid=ICCID-123456789&country=CO'));
        $this->assertAuthenticatedAs($user);
    }

    private function ensureActiveUserStatus(): int
    {
        $status = Status::query()
            ->where('name', 'status_active')
            ->where('type', 'user')
            ->first();

        if (!$status) {
            $status = Status::create([
                'name' => 'status_active',
                'type' => 'user',
                'class' => 'success',
            ]);
        }

        return (int) $status->id;
    }
}