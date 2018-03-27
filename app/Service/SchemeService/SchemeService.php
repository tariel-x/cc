<?php
namespace App\Service\SchemeService;

use App\Service\SchemeStorage\SchemeStorageInterface;
use App\Service\SchemeStorage\Models\Contract as ContractModel;

class SchemeService
{
    /**
     * @var SchemeStorageInterface
     */
    private $storage;

    /**
     * SchemeService constructor.
     *
     * @param SchemeStorageInterface $storage
     */
    public function __construct(SchemeStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return SchemeStorageInterface
     */
    public function getStorage(): SchemeStorageInterface
    {
        return $this->storage;
    }

    /**
     * Register new service with scheme
     *
     * @param array $schemes
     * @param array $service
     * @return bool
     */
    public function register(array $schemes, array $service): bool
    {
        
        $existing = $this->get($schemes);
        if ($existing !== null) {
            return $this->registerExisting($existing, $contract);
        }
        return $this->registerNew($schemes, $service);
    }

    private function registerNew(Contract $contract): bool
    {
        $model = (new ModelBuilder())->createContractModel($contract);
        return $this->getStorage()->save($model);
    }

    private function registerExisting(ContractModel $model, Contract $contract): bool
    {
        $model = (new ModelBuilder())->appendService($model, $contract);
        return $this->getStorage()->save($model);
    }

    public function getAll(): array
    {
        return (array)$this->getStorage()->getAll();
    }

    public function get(array $schemes)
    {
        return $this->getStorage()->get($schemes);
    }

    /**
     * Get service names resolving contract
     *
     * @param array $schemas
     * @return array
     */
    public function resolveContract(array $schemas): array
    {
        $models = $this->makeSchemes($schemas);
        $contracts = $this->getStorage()->getAll();

        $contracts = array_filter($contracts, function (Contract $contract) use ($models) {
            return $this->compareSchemaQueue($contract->getScheme(), $models);
        });

        return array_map(function (Contract $contract) {
            return $contract->getService();
        }, $contracts);
    }

    /**
     * Compares
     * @param Scheme[] $schemes1
     * @param Scheme[] $schemes2
     * @return bool
     */
    private function compareSchemaQueue(array $schemes1, array $schemes2): bool
    {
        if ($this->compareByHash($schemes1, $schemes2)) {
            return true;
        }
        //todo another check
        return false;
    }

    /**
     * @param Scheme[] $schemes1
     * @param Scheme[] $schemes2
     * @return bool
     */
    private function compareByHash(array $schemes1, array $schemes2): bool
    {
        $hashes1 = array_map(function (Scheme $scheme) {
            return md5(json_encode($scheme));
        }, $schemes1);
        $hashes2 = array_map(function (Scheme $scheme) {
            return md5(json_encode($scheme));
        }, $schemes2);
        if (count(array_diff($hashes1, $hashes2)) === 0) {
            return true;
        }
        return false;
    }
}