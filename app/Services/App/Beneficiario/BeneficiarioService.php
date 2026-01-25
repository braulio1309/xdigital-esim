<?php

namespace App\Services\App\Beneficiario;

use App\Models\App\Beneficiario\Beneficiario;
use App\Services\App\AppService;

class BeneficiarioService extends AppService
{
    public function __construct(Beneficiario $beneficiario)
    {
        $this->model = $beneficiario;
    }

    /**
     * Update Beneficiario service
     * @param Beneficiario $beneficiario
     * @return Beneficiario
     */
    public function update(Beneficiario $beneficiario)
    {
        $beneficiario->fill(request()->all());

        $this->model = $beneficiario;

        $beneficiario->save();

        return $beneficiario;
    }

    /**
     * Delete Beneficiario service
     * @param Beneficiario $beneficiario
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Beneficiario $beneficiario)
    {
        return $beneficiario->delete();
    }
}
