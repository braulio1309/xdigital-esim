<?php

namespace Database\Seeders;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Cliente\Cliente;
use App\Models\App\Transaction\Transaction;
use App\Models\Core\Auth\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BeneficiarioClienteSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        // Create a test beneficiario with user
        $beneficiarioUser = User::firstOrCreate(
            ['email' => 'beneficiario.test@example.com'],
            [
                'first_name' => 'Juan',
                'last_name' => 'Beneficiario',
                'password' => Hash::make('Juan123'),
                'user_type' => 'beneficiario',
                'status_id' => 1,
            ]
        );

        $beneficiario = Beneficiario::firstOrCreate(
            ['user_id' => $beneficiarioUser->id],
            [
                'nombre' => 'Juan',
                'descripcion' => 'Beneficiario de prueba',
                'commission_percentage' => 0.00,
                'total_earnings' => 0.00,
                'total_sales' => 0,
            ]
        );

        $this->command->info("Beneficiario created: {$beneficiario->nombre} (email: {$beneficiarioUser->email}, password: Juan123)");

        // Create a test cliente with user
        $clienteUser = User::firstOrCreate(
            ['email' => 'cliente.test@example.com'],
            [
                'first_name' => 'Maria',
                'last_name' => 'Cliente',
                'password' => Hash::make('Maria123'),
                'user_type' => 'cliente',
                'status_id' => 1,
            ]
        );

        $cliente = Cliente::firstOrCreate(
            ['user_id' => $clienteUser->id],
            [
                'nombre' => 'Maria',
                'apellido' => 'Cliente',
                'email' => 'cliente.test@example.com',
            ]
        );

        $this->command->info("Cliente created: {$cliente->nombre} {$cliente->apellido} (email: {$clienteUser->email}, password: Maria123)");

        // Create some test transactions for the cliente
        $transaction1 = Transaction::firstOrCreate(
            ['transaction_id' => 'TEST-TRX-001'],
            [
                'status' => 'completed',
                'iccid' => '8934071234567890123',
                'esim_qr' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
                'creation_time' => now()->subDays(10),
                'cliente_id' => $cliente->id,
            ]
        );

        $transaction2 = Transaction::firstOrCreate(
            ['transaction_id' => 'TEST-TRX-002'],
            [
                'status' => 'completed',
                'iccid' => '8934071234567890124',
                'esim_qr' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
                'creation_time' => now()->subDays(5),
                'cliente_id' => $cliente->id,
            ]
        );

        $transaction3 = Transaction::firstOrCreate(
            ['transaction_id' => 'TEST-TRX-003'],
            [
                'status' => 'completed',
                'iccid' => '8934071234567890125',
                'esim_qr' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
                'creation_time' => now()->subDays(1),
                'cliente_id' => $cliente->id,
            ]
        );

        $this->command->info("Created {$cliente->transactions()->count()} transactions for cliente");
    }
}
