<?php
namespace App\Service\SchemeStorage;

class InMemoryStorage implements SchemeStorageInterface
{
    /**
     * schemas storage
     *
     * @var array
     */
    private $schemes = [];

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
        if (!array_key_exists($contract->getName(), $this->schemes)) {
            $this->schemes[$contract->getName()] = [];
        }
        $this->schemes[$contract->getName()][] = $contract;
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
     * @param array $scheme
     * @return string
     */
    private function hash(array $scheme): string
    {
        return md5(json_encode($scheme));
    }

    /**
     * Get schemes by name
     *
     * @param string $name
     * @return Contract[]
     */
    public function get(string $name): array
    {
        if (!array_key_exists($name, $this->schemes)) {
            return [];
        }
        return $this->schemes[$name];
    }

    /**
     * Get all schemes
     *
     * @return Contract[]
     */
    public function getAll(): array
    {
        return array_reduce($this->schemes, function (array $carry, array $item) {
            return array_merge($carry, array_values($item));
        }, []);
    }

    /**
     * Get service definition
     *
     * @param array $scheme
     * @return string[]
     */
    public function getNameByScheme(array $scheme): array
    {
        $hash = $this->hash($scheme);
        if (!array_key_exists($hash, $this->index)) {
            return [];
        }
        return $this->index[$hash];
    }

    /**
     * Remove schemes by name
     *
     * @param string $name
     * @return bool
     */
    public function removeSchemes(string $name): bool
    {
        unset($this->schemes[$name]);
        $this->index = array_filter($this->index, function ($item) use ($name) {
            return $item === $name;
        });
        return true;
    }
}