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

    public function appendService(ContractModel $model, Contract $contract)
    {
        $model->addService($contract->getService()->jsonSerialize());
    }

    public function removeService(ContractModel $model, Contract $contract)
    {
        $removeHash = md5(json_encode(array_multisort($contract->getService()->jsonSerialize())));
        $services = array_filter($model->getServices(), function (array $service) use ($removeHash) {
            $hash = md5(json_encode(array_multisort($service)));
            return $hash !== $removeHash;
        });
        $model->setServices($services);
    }
}