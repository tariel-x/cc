<?php
namespace App\Service\SchemeStorage;

use App\Service\SchemeService\Contract;
use App\Service\SchemeService\Scheme;
use App\Service\SchemeService\Service;

class InMemoryStorage implements SchemeStorageInterface
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
     * Save scheme
     *
     * @param Contract $contract
     * @return bool
     */
    public function save(Contract $contract): bool
    {
        if (!array_key_exists($contract->getName(), $this->contracts)) {
            $this->contracts[$contract->getName()] = [];
        }
        $this->contracts[$contract->getName()][] = $contract;
        $this->addIndex($contract);
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
        if (!array_key_exists($hash, $this->index)) {
            $this->index[$hash] = [];
        }
        $this->index[$hash][] = $contract->getName();
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