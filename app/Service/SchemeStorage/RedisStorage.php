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
        $hash = $this->hash($contract->getSchemes());
        $rawContract = $contract->jsonSerialize();
        array_multisort($rawContract);
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
    private function hash(array $data): string
    {
        (new \App\Service\Helper())->sort($data);
        return self::CONTRACT_PREFIX . md5(json_encode($data));
    }

    /**
     * Get all contracts
     *
     * @return Contract[]
     */
    public function getAll(): array
    {
        $builder = new ModelBuilder();
        $keys = $this->redis->keys('*');
        $data = array_map(function (string $key) {
            return json_decode($this->redis->get($key), true);
        }, $keys);
        return array_map(function ($item) use ($builder) {
            return $builder->modelFromRaw($item);
        }, $data);
    }

    public function get(array $schemes): ?Contract
    {
        $hash = $this->hash($schemes);
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
        $hash = $this->hash($schemes);
        return $this->redis->del($hash);
    }
}