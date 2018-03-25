<?php
namespace App\Service\SchemeStorage;

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
        $hash = $this->hash($contract->getScheme());
        $value = $this->redis->get($hash);
        if ($value === false) {
            $this->addIndex($contract);
        }
        printf("New hash %s\n", $hash);
        return true;
    }

    /**
     * Build scheme index
     *
     * @param Contract $contract
     */
    private function addIndex(Contract $contract)
    {
        $hash = $this->hash($contract->getScheme());
        $this->redis->set($hash, json_encode($contract));
    }

    private function updateIndex(Contract $contract)
    {
        $hash = $this->hash($contract->getScheme());
        $value = json_decode($this->redis->get($hash), true);
        $value['service'][] = $contract->getService();
    }

    /**
     * make scheme hash
     *
     * @param array $contract
     * @return string
     */
    private function hash(array $contract): string
    {
        return md5(json_encode($contract));
    }

    /**
     * Get contracts by name
     *
     * @param string $name
     * @return Contract[]
     */
    public function get(string $name): array
    {
        if (!array_key_exists($name, $this->contracts)) {
            return [];
        }
        return $this->contracts[$name];
    }

    /**
     * Get all contracts
     *
     * @return Contract[]
     */
    public function getAll(): array
    {
        return array_reduce($this->contracts, function (array $carry, array $item) {
            return array_merge($carry, array_values($item));
        }, []);
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