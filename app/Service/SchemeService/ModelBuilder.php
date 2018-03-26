<?php

namespace App\Service\SchemeService;

use App\Service\SchemeStorage\Models\Contract as ContractModel;

class ModelBuilder
{
    public function createContractModel(Contract $contract): ContractModel
    {
        return new ContractModel(
            (array)$contract->getSchemes(),
            [(array)$contract->getService()]
        );
    }

    public function modelFromRaw(array $data): ContractModel
    {
        return new ContractModel($data['schemes'], $data['services']);
    }
}