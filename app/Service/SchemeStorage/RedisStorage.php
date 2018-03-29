<?php
namespace App\Service\SchemeStorage;

use App\Service\Helper;
use App\Service\SchemeStorage\Models\Contract;
use App\Service\SchemeService\ModelBuilder;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * Class RedisStorage
 * @package App\Service\SchemeStorage
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class RedisStorage implements SchemeStorageInterface
{
    use LoggerAwareTrait;

    const CONTRACT_PREFIX = 'contract_';

    /**
     * redis
     *
     * @var \Redis
     */
    private $redis;

    /**
     * RedisStorage constructor.
     * @param \Redis $redis
     */
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

    /**
     * Get contract by scheme
     * @param array $schemes
     * @return Contract|null
     */
    public function getContract(array $schemes): ?Contract
    {
        return $this->get($schemes, self::CONTRACT_PREFIX);
    }

    /**
     * Remove contract by scheme
     * @param array $schemes
     * @return bool
     */
    public function removeContract(array $schemes): bool
    {
        return $this->remove($schemes, self::CONTRACT_PREFIX);
    }

    /**
     * make scheme hash
     *
     * @param array $data
     * @param string $prefix
     * @return string
     */
    private function hash(array $data, string $prefix): string
    {
        (new Helper())->sort($data);
        return $prefix . md5(json_encode($data));
    }

    private function save(Contract $contract, string $prefix): bool
    {
        $hash = $this->hash($contract->getSchemes(), $prefix);
        $rawContract = $contract->jsonSerialize();
        (new Helper())->sort($rawContract);
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
            $this->logger->debug(sprintf('No contracts found with hash `%s`', $hash));
            return null;
        }
        $this->logger->debug(sprintf('Contract found with hash `%s`', $hash));
        return (new ModelBuilder())->modelFromRaw(json_decode($data, true));
    }

    private function remove(array $schemes, string $prefix): bool
    {
        $hash = $this->hash($schemes, $prefix);
        return $this->redis->del($hash);
    }
}
