<?php

namespace Tests\Feature\App\Cliente;

use App\Imports\App\ClienteImport;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\Type;
use App\Models\Core\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClienteImportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_imports_clientes_from_contratante_columns_when_nombre_and_apellido_are_missing(): void
    {
        Mail::fake();
        $this->createClienteRoleAndStatus();

        $import = new ClienteImport(null, [], 3, null);

        $import->collection(collect([
            collect([
                'Voucher' => 'VOUCHER-SEA-1',
                'correo del contratante' => 'contratante@example.com',
                'nombre del contratante' => 'Juan Perez',
                'cant de pasajeros' => 4,
                'id del contratante' => 'DNI-7788',
            ]),
        ]));

        $this->assertSame(1, $import->getImported());
        $this->assertSame(0, $import->getSkipped());

        $this->assertDatabaseHas('clientes', [
            'nombre' => 'Juan Perez',
            'apellido' => '',
            'email' => 'contratante@example.com',
            'identificador' => 'DNI-7788',
            'free_esim_capacity' => 3,
        ]);

        $this->assertDatabaseHas('cliente_vouchers', [
            'numero_voucher' => 'VOUCHER-SEA-1',
            'numero_personas' => 4,
        ]);
    }

    private function createClienteRoleAndStatus(): void
    {
        if (!Type::query()->where('alias', 'app')->exists()) {
            Type::create([
                'name' => 'App',
                'alias' => 'app',
            ]);
        }

        if (!Status::query()->where('name', 'status_active')->where('type', 'user')->exists()) {
            Status::create([
                'name' => 'status_active',
                'type' => 'user',
                'class' => 'success',
            ]);
        }

        if (!Role::query()->where('name', 'cliente')->exists()) {
            Role::create([
                'name' => 'cliente',
                'type_id' => Type::findByAlias('app')->id,
                'is_admin' => false,
                'is_default' => false,
            ]);
        }
    }
}