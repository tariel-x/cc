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
    public function register(array $schemes, array $service): bool
    {
        $contract = (new ContractBuilder())->build($schemes, $service);
        $existing = $this->getStorage()->get($schemes);
        if ($existing !== null) {
            $this->logger->debug('Contract exists, updating');
            return $this->registerExisting($existing, $contract);
        }
        $this->logger->debug('New contract');
        return $this->registerNew($contract);
    }

    private function registerNew(Contract $contract): bool
    {
        $model = (new ModelBuilder())->createContractModel($contract);
        return $this->getStorage()->save($model);
    }

    private function registerExisting(ContractModel $model, Contract $contract): bool
    {
        (new ModelBuilder())->appendService($model, $contract);
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

    public function remove(array $schemes, array $service): bool
    {
        $existing = $this->get($schemes);
        if ($existing === null) {
            return true;
        }
        $contract = (new ContractBuilder())->build($schemes, $service);
        $existing = (new ModelBuilder())->removeService($existing, $contract);
        if (empty($existing->getServices())) {
            return $this->getStorage()->remove($schemes);
        }
        return $this->getStorage()->save($existing);
    }
}