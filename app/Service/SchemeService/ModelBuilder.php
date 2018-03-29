<?php
namespace App\Service\SchemeService;

use App\Service\Helper;
use App\Service\SchemeService\Models\Contract;
use App\Service\SchemeService\Models\Scheme;
use App\Service\SchemeStorage\Models\Contract as ContractModel;

/**
 * ModelBuilder operates with redis model
 * @package App\Service\SchemeService
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class ModelBuilder
{
    /**
     * Create redis model from input model
     * @param Contract $contract
     * @return ContractModel
     */
    public function createContractModel(Contract $contract): ContractModel
    {
        $schemes = array_map(function (Scheme $scheme) {
            return $scheme->jsonSerialize();
        }, $contract->getSchemes());

        $model = new ContractModel($schemes);
        if ($contract->getService() !== null) {
            $model->addService($contract->getService()->jsonSerialize());
        }
        if ($contract->getUsage() !== null) {
            $model->addUsage($contract->getUsage()->jsonSerialize());
        }
        return $model;
    }

    /**
     * Make redis model from raw array
     * @param array $data
     * @return ContractModel
     */
    public function modelFromRaw(array $data): ContractModel
    {
        $model = new ContractModel($data['schemes']);
        $model->setServices($data['services']);
        $model->setUsages($data['usages']);
        return $model;
    }

    /**
     * Appends service info from contract model
     * @param ContractModel $model
     * @param Contract $contract
     * @return ContractModel
     */
    public function appendService(ContractModel $model, Contract $contract): ContractModel
    {
        if ($contract->getService() === null) {
            return $model;
        }
        $rawNewService = $contract->getService()->jsonSerialize();
        if ($this->serviceExists($model->getServices(), $rawNewService)) {
            return $model;
        }
        $model->addService($rawNewService);
        return $model;
    }

    /**
     * Appends usage info from contract service
     * @param ContractModel $model
     * @param Contract $contract
     * @return ContractModel
     */
    public function appendUsage(ContractModel $model, Contract $contract): ContractModel
    {
        if ($contract->getUsage() === null) {
            return $model;
        }
        $rawNewService = $contract->getUsage()->jsonSerialize();
        if ($this->serviceExists($model->getUsages(), $rawNewService)) {
            return $model;
        }
        $model->addUsage($rawNewService);
        return $model;
    }

    private function serviceExists(array $services, array $rawNewService): bool
    {
        $newHash = $this->hash($rawNewService);
        $hashes = array_map(function (array $service) {
            return $this->hash($service);
        }, $services);

        if (in_array($newHash, $hashes)) {
            return true;
        }
        return false;
    }

    /**
     * Remove service
     * @param ContractModel $model
     * @param Contract $contract
     * @return ContractModel
     */
    public function removeService(ContractModel $model, Contract $contract): ContractModel
    {
        if ($contract->getService() === null) {
            return $model;
        }
        $services = $this->removeServiceItem($model->getServices(), $contract->getService()->jsonSerialize());
        $model->setServices($services);
        return $model;
    }

    /**
     * Remove usage
     * @param ContractModel $model
     * @param Contract $contract
     * @return ContractModel
     */
    public function removeUsage(ContractModel $model, Contract $contract): ContractModel
    {
        if ($contract->getUsage() === null) {
            return $model;
        }
        $services = $this->removeServiceItem($model->getUsages(), $contract->getUsage()->jsonSerialize());
        $model->setUsages($services);
        return $model;
    }

    private function removeServiceItem(array $serivces, array $rawNewService): array
    {
        $removeHash = $this->hash($rawNewService);
        return array_filter($serivces, function (array $service) use ($removeHash) {
            $hash = $this->hash($service);
            return $hash !== $removeHash;
        });
    }

    private function hash(array $data): string
    {
        (new Helper())->sort($data);
        return md5(json_encode($data));
    }
}
