<?php

namespace App\Service\SchemeService;

use App\Service\SchemeStorage\Models\Contract as ContractModel;

class ModelBuilder
{
    public function createContractModel(Contract $contract): ContractModel
    {
        $schemes = array_map(function (Scheme $scheme) {
            return $scheme->jsonSerialize();
        }, $contract->getSchemes());

        return new ContractModel(
            $schemes,
            [$contract->getService()->jsonSerialize()]
        );
    }

    public function modelFromRaw(array $data): ContractModel
    {
        return new ContractModel($data['schemes'], $data['services']);
    }
}