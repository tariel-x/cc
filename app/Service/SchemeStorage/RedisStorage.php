<?php
namespace App\Service\SchemeStorage;

use App\Service\SchemeStorage\Models\Contract;
use App\Service\SchemeService\ModelBuilder;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class RedisStorage implements SchemeStorageInterface
{
    use LoggerAwareTrait;

    const CONTRACT_PREFIX = 'contract_';

    const DEPENDENCY_PREFIX = 'dep_';

    /**
     * redis
     *
     * @var \Redis
     */
    private $redis;

    public function __construct($redis)
    {
        $this->redis = $redis;
        $this->logger = new NullLogger();
    }

    /**
     * Save scheme
     *
     * @param Contract $contract
     * @return bool
     */
    public function saveContract(Contract $contract): bool
    {
        return $this->save($contract, self::CONTRACT_PREFIX);
    }

    /**
     * Get all contracts
     *
     * @return Contract[]
     */
    public function getAllContracts(): array
    {
        return $this->getAll(self::CONTRACT_PREFIX);
    }

    public function getContract(array $schemes): ?Contract
    {
        return $this->get($schemes, self::CONTRACT_PREFIX);
    }

    public function removeContract(array $schemes): bool
    {
        return $this->remove($schemes, self::CONTRACT_PREFIX);
    }

    public function getAllDependecies(): array
    {
        return $this->getAll(self::DEPENDENCY_PREFIX);
    }

    /**
     * Save dependency
     * @param Contract $contract
     * @return bool
     */
    public function saveDependency(Contract $contract): bool
    {
        return $this->save($contract, self::DEPENDENCY_PREFIX);
    }

    /**
     * Get dependency by scheme
     *
     * @param array $schemes
     * @return Contract
     */
    public function getDependency(array $schemes): ?Contract
    {
        return $this->get($schemes, self::DEPENDENCY_PREFIX);
    }

    /**
     * Remove dependency
     *
     * @param array $schemes
     * @return boolean
     */
    public function removeDependency(array $schemes): bool
    {
        return $this->remove($schemes, self::DEPENDENCY_PREFIX);
    }

    /**
     * make scheme hash
     *
     * @param array $data
     * @return string
     */
    private function hash(array $data, string $prefix): string
    {
        (new \App\Service\Helper())->sort($data);
        return $prefix . md5(json_encode($data));
    }

    private function save(Contract $contract, string $prefix): bool
    {
        $hash = $this->hash($contract->getSchemes(), $prefix);
        $rawContract = $contract->jsonSerialize();
        (new \App\Service\Helper())->sort($rawContract);
        $this->redis->set($hash, json_encode($rawContract));
        $this->logger->debug(sprintf('Setted contract with hash `%s`', $hash));
        return true;
    }

    private function getAll(string $prefix): array
    {
        $builder = new ModelBuilder();
        $keys = $this->redis->keys($prefix . '*');
        $data = array_map(function (string $key) {
            return json_decode($this->redis->get($key), true);
        }, $keys);
        return array_map(function ($item) use ($builder) {
            return $builder->modelFromRaw($item);
        }, $data);
    }

    private function get(array $schemes, string $prefix): ?Contract
    {
        $hash = $this->hash($schemes, $prefix);
        $data = $this->redis->get($hash);
        if ($data === false) {
            $this->logger->debug(sprintf("No contracts found with hash `%s`", $hash));
            return null;
        }
        $this->logger->debug(sprintf("Contract found with hash `%s`", $hash));
        return (new ModelBuilder())->modelFromRaw(json_decode($data, true));
    }

    private function remove(array $schemes, string $prefix): bool
    {
        $hash = $this->hash($schemes, $prefix);
        return $this->redis->del($hash);
    }
}
