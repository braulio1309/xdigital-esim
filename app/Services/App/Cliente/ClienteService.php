<?php

namespace App\Services\App\Cliente;

use App\Models\App\Cliente\Cliente;
use App\Services\App\AppService;

class ClienteService extends AppService
{
    public function __construct(Cliente $cliente)
    {
        $this->model = $cliente;
    }

    /**
     * Update Cliente service
     * @param Cliente $cliente
     * @return Cliente
     */
    public function update(Cliente $cliente)
    {
        $cliente->fill(request()->all());

        $this->model = $cliente;

        $cliente->save();

        return $cliente;
    }

    /**
     * Delete Cliente service
     * @param Cliente $cliente
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Cliente $cliente)
    {
        return $cliente->delete();
    }
}
