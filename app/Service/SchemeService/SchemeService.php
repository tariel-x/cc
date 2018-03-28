<?php
namespace App\Service\SchemeService;

use App\Service\SchemeStorage\SchemeStorageInterface;
use App\Service\SchemeStorage\Models\Contract as ContractModel;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class SchemeService
{
    use LoggerAwareTrait;
    
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
        $this->logger = new NullLogger();
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
    public function registerContract(array $schemes, array $service): bool
    {
        $contract = (new ContractBuilder())->buildContract($schemes, $service);
        $existing = $this->getStorage()->getContract($schemes);
        if ($existing !== null) {
            $this->logger->debug('Contract exists, updating');
            (new ModelBuilder())->appendService($existing, $contract);
            return $this->getStorage()->saveContract($existing);
        }
        $this->logger->debug('New contract');
        return $this->registerNew($contract);
    }

    /**
     * Register new service using scheme
     *
     * @param array $schemes
     * @param array $service
     * @return bool
     */
    public function registerUsage(array $schemes, array $service): bool
    {
        $contract = (new ContractBuilder())->buildUsageContract($schemes, $service);
        $existing = $this->getStorage()->getContract($schemes);
        if ($existing !== null) {
            $this->logger->debug('Contract exists, updating');
            (new ModelBuilder())->appendUsage($existing, $contract);
            return $this->getStorage()->saveContract($existing);
        }
        $this->logger->debug('New contract');
        return $this->registerNew($contract);
    }

    private function registerNew(Contract $contract): bool
    {
        $model = (new ModelBuilder())->createContractModel($contract);
        return $this->getStorage()->saveContract($model);
    }

    public function getAll(): array
    {
        return (array)$this->getStorage()->getAllContracts();
    }

    public function get(array $schemes)
    {
        return $this->getStorage()->getContract($schemes);
    }

    public function removeContract(array $schemes, array $service): bool
    {
        $existing = $this->getContract($schemes);
        if ($existing === null) {
            return true;
        }
        $contract = (new ContractBuilder())->buildContract($schemes, $service);
        $existing = (new ModelBuilder())->removeService($existing, $contract);
        return $this->removeOrUpdate($existing);
    }

    public function removeUsage(array $schemes, array $service): bool
    {
        $existing = $this->getContract($schemes);
        if ($existing === null) {
            return true;
        }
        $contract = (new ContractBuilder())->buildUsageContract($schemes, $service);
        $existing = (new ModelBuilder())->removeUsage($existing, $contract);
        return $this->removeOrUpdate($existing);
    }

    private function removeOrUpdate(ContractModel $existing): bool
    {
        if (empty($existing->getUsages())) {
            return $this->getStorage()->removeContract($existing->getSchemes());
        }
        return $this->getStorage()->saveContract($existing);
    }
}