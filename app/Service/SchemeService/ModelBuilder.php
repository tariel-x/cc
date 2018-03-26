<?php

namespace App\Service\SchemeService;

use App\Service\SchemeStorage\Models\Contract as ContractModel;

class ModelBuilder
{
    public function createContractModel(Contract $contract): ContractModel
    {
        return new ContractModel(
            $contract->getSchemes(),
            [$contract->getService()]
        );
    }
}