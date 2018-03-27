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

    public function appendService(ContractModel $model, Contract $contract): ContractModel
    {
        $rawNewService = $contract->getService()->jsonSerialize();
        $newHash = $this->hash($rawNewService);

        $hashes = array_map(function (array $service) {
            return $this->hash($service);
        }, $model->getServices());

        if (in_array($newHash, $hashes)) {
            return $model;
        }

        $model->addService($rawNewService);
        return $model;
    }

    public function removeService(ContractModel $model, Contract $contract): ContractModel
    {
        $removeHash = $this->hash($contract->getService()->jsonSerialize());
        $services = array_filter($model->getServices(), function (array $service) use ($removeHash) {
            $hash = $this->hash($service);
            return $hash !== $removeHash;
        });
        $model->setServices($services);
        return $model;
    }

    private function hash(array $data): string
    {
        (new \App\Service\Helper())->sort($data);
        return md5(json_encode($data));
    }
}