<?php
namespace App\Service\SchemeStorage;

use App\Service\SchemeStorage\Models\Contract;
use App\Service\SchemeService\ModelBuilder;

class RedisStorage implements SchemeStorageInterface
{
    /**
     * schemas storage
     *
     * @var array
     */
    private $contracts = [];

    /**
     * scheme index
     *
     * @var array
     */
    private $index = [];

    /**
     * redis
     *
     * @var \Redis
     */
    private $redis;

    public function __construct($redis)
    {
        $this->redis = $redis;
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
        $this->redis->set($hash, json_encode($rawContract));
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
        return md5(json_encode($data));
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

    /**
     * Get service definition
     *
     * @param array $scheme
     * @return string[]
     */
    public function getNameByContract(array $scheme): array
    {
        $hash = $this->hash($scheme);
        if (!array_key_exists($hash, $this->index)) {
            return [];
        }
        return $this->index[$hash];
    }

    /**
     * Remove contracts by name
     *
     * @param string $name
     * @return bool
     */
    public function removeContracts(string $name): bool
    {
        unset($this->contracts[$name]);
        $this->index = array_filter($this->index, function ($item) use ($name) {
            return $item === $name;
        });
        return true;
    }
}