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
    public function save(Contract $contract): bool
    {
        $hash = $this->hash($contract->getSchemes(), self::CONTRACT_PREFIX);
        $rawContract = $contract->jsonSerialize();
        (new \App\Service\Helper())->sort($rawContract);
        $this->redis->set($hash, json_encode($rawContract));
        $this->logger->debug(sprintf('Setted contract with hash `%s`', $hash));
        return true;
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

    /**
     * Get all contracts
     *
     * @return Contract[]
     */
    public function getAll(): array
    {
        return $this->getAllModels(self::CONTRACT_PREFIX);
    }

    private function getAllModels(string $prefix): array
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

    public function get(array $schemes): ?Contract
    {
        $hash = $this->hash($schemes, self::CONTRACT_PREFIX);
        $data = $this->redis->get($hash);
        if ($data === false) {
            $this->logger->debug(sprintf("No contracts found with hash `%s`", $hash));
            return null;
        }
        $this->logger->debug(sprintf("Contract found with hash `%s`", $hash));
        return (new ModelBuilder())->modelFromRaw(json_decode($data, true));
    }

    public function remove(array $schemes): bool
    {
        $hash = $this->hash($schemes, self::CONTRACT_PREFIX);
        return $this->redis->del($hash);
    }

    public function getAllDependecies(): array
    {
        return $this->getAllModels(self::DEPENDENCY_PREFIX);
    }
}