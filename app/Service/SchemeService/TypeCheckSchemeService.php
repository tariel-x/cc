<?php
namespace App\Service\SchemeService;

use App\Service\SchemeService\Models\Contract;
use App\Service\SchemeStorage\SchemeStorageInterface;
use App\Service\SchemeStorage\Models\Contract as ContractModel;
use App\Service\TypeChecker\TypeCheckerInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * Class SchemeService
 * @package App\Service\SchemeService
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class TypeCheckSchemeService implements SchemeServiceInterface
{
    use LoggerAwareTrait;
    
    /**
     * @var SchemeStorageInterface
     */
    private $storage;

    /**
     * @var TypeCheckerInterface
     */
    private $checker;

    /**
     * SchemeService constructor.
     *
     * @param SchemeStorageInterface $storage
     * @param TypeCheckerInterface $checker
     */
    public function __construct(SchemeStorageInterface $storage, TypeCheckerInterface $checker)
    {
        $this->storage = $storage;
        $this->checker = $checker;
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
     * @return TypeCheckerInterface
     */
    public function getChecker(): TypeCheckerInterface
    {
        return $this->checker;
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
        $noProviders = array_filter($contracts, function (ContractModel $contract) {
            return empty($contract->getServices()) && !empty($contract->getUsages());
        });
        $this->logger->debug(sprintf("found countracts without providers: %d", count($noProviders)));
        return array_filter($noProviders, function (ContractModel $noProvider) use ($contracts) {
            return !array_reduce($contracts, function (bool $carry, ContractModel $contract) use ($noProvider) {
                return $carry && $this->contractsCompatible($contract, $noProvider);
            }, true);
        });
    }

    /**
     * Checks that user contract is compatible with provider contract
     * @param ContractModel $provider
     * @param ContractModel $user
     * @return bool
     */
    private function contractsCompatible(ContractModel $provider, ContractModel $user): bool
    {
        $result = true;
        foreach ($provider->getSchemes() as $key => $scheme) {
            if (!array_key_exists($key, $user->getSchemes())) {
                return false;
            }
            $userScheme = $user->getSchemes()[$key];
            $result = $result && $this->getChecker()->compare($scheme['scheme'], $userScheme['scheme']);
        }
        return $result;
    }

    /**
     * Get suitable contracts with providers for passed schemes
     * @param array $schemes
     * @return array
     */
    public function resolve(array $schemes): array
    {
        $reqContract = (new ContractBuilder())->build($schemes);
        $reqContract = (new ModelBuilder())->createContractModel($reqContract);
        /** @var ContractModel $reqContract */
        /** @var ContractModel[] $contracts */
        $contracts = $this->getStorage()->getAllContracts();
        return array_values(array_filter($contracts, function (ContractModel $contract) use ($reqContract) {
            return $this->contractsCompatible($contract, $reqContract) && !empty($contract->getServices());
        }));
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
