<?php
namespace App\Service\SchemeService;

use App\Service\SchemeService\Models\Contract;
use App\Service\SchemeStorage\SchemeStorageInterface;
use App\Service\SchemeStorage\Models\Contract as ContractModel;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * Class SchemeService
 * @package App\Service\SchemeService
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class SchemeService implements SchemeServiceInterface
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

    /**
     * @param Contract $contract
     * @return bool
     */
    private function registerNew(Contract $contract): bool
    {
        $model = (new ModelBuilder())->createContractModel($contract);
        return $this->getStorage()->saveContract($model);
    }

    /**
     * @return ContractModel[]
     */
    public function getAll(): array
    {
        return (array)$this->getStorage()->getAllContracts();
    }

    /**
     * @param array $schemes
     * @return ContractModel|null
     */
    public function get(array $schemes)
    {
        return $this->getStorage()->getContract($schemes);
    }

    /**
     * Remove contract
     * @param array $schemes
     * @param array $service
     * @return bool
     */
    public function removeContract(array $schemes, array $service): bool
    {
        $existing = $this->getStorage()->getContract($schemes);
        if ($existing === null) {
            return true;
        }
        $contract = (new ContractBuilder())->buildContract($schemes, $service);
        $existing = (new ModelBuilder())->removeService($existing, $contract);
        return $this->removeOrUpdate($existing);
    }

    /**
     * Remove usage of contract
     * @param array $schemes
     * @param array $service
     * @return bool
     */
    public function removeUsage(array $schemes, array $service): bool
    {
        $existing = $this->getStorage()->getContract($schemes);
        if ($existing === null) {
            return true;
        }
        $contract = (new ContractBuilder())->buildUsageContract($schemes, $service);
        $existing = (new ModelBuilder())->removeUsage($existing, $contract);
        return $this->removeOrUpdate($existing);
    }

    /**
     * Removes if no services or usages
     * @param ContractModel $existing
     * @return bool
     */
    private function removeOrUpdate(ContractModel $existing): bool
    {
        if (empty($existing->getUsages()) && empty($existing->getServices())) {
            $this->logger->debug('Contract does not contain services or usages, remove it.');
            return $this->getStorage()->removeContract($existing->getSchemes());
        }
        return $this->getStorage()->saveContract($existing);
    }

    /**
     * Returns contract without services, but with usages
     * @return ContractModel[]|array
     */
    public function getProblems(): array
    {
        $contracts = $this->getStorage()->getAllContracts();
        return array_filter($contracts, function (ContractModel $contract) {
            return empty($contract->getServices()) && !empty($contract->getUsages());
        });
    }

    /**
     * Synonym for get
     * @param array $schemes
     * @return array
     */
    public function resolve(array $schemes): array
    {
        $found = [$this->get($schemes)];
        return $found ?? [];
    }

    /**
     * Check is service removing breaks contract usage
     * @param array $schemes
     * @param array $service
     * @return bool
     */
    public function isRemoval(array $schemes, array $service): bool
    {
        $existing = $this->getStorage()->getContract($schemes);
        if ($existing === null) {
            return true;
        }
        $contract = (new ContractBuilder())->buildContract($schemes, $service);
        $existing = (new ModelBuilder())->removeService($existing, $contract);
        return !(empty($existing->getServices()) && !empty($existing->getUsages()));
    }
}
