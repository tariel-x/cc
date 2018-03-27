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
        $contract = (new ContractBuilder())->build($schemes, $service);
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

    public function remove(array $schemes, array $service)
    {
        $existing = $this->get($schemes);
        if ($existing === null) {
            return;
        }
        $contract = (new ContractBuilder())->build($schemes, $service);
        $existing = (new ModelBuilder())->removeService($existing, $contract);
        if (empty($existing->getServices())) {
            return $this->getStorage()->remove($schemes);
        }
        return $this->getStorage()->save($existing);
    }
}