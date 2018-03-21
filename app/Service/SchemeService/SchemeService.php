<?php
namespace App\Service\SchemeService;

use App\Service\SchemeStorage\Contract;
use App\Service\SchemeStorage\Scheme;
use App\Service\SchemeStorage\SchemeStorageInterface;

class SchemeService
{
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
     * @param string $name
     * @return bool
     */
    public function register(array $schemes, array $service, string $name): bool
    {
        $contract = new Contract($name, $this->makeSchemes($schemes), $service);
        return $this->getStorage()->save($contract);
    }

    /**
     * @param array $schemes
     * @return Scheme[]
     */
    private function makeSchemes(array $schemes): array
    {
        return array_map(function (array $item) {
            return new Scheme(
                (bool)$item['in'],
                $item['scheme'],
                (string)$item['type']
            );
        }, $schemes);
    }

    /**
     * Unregister service
     *
     * @param string $name
     * @return bool
     */
    public function forgetService(string $name): bool
    {
        return $this->getStorage()->removeContracts($name);
    }

    /**
     * Get service names resolving contract
     *
     * @param array $schemas
     * @return array
     */
    public function resolveContract(array $schemas): array
    {
        $models = $this->makeSchemes($schemas);
        $contracts = $this->getStorage()->getAll();

        $contracts = array_filter($contracts, function (Contract $contract) use ($models) {
            return $this->compareSchemaQueue($contract->getScheme(), $models);
        });

        return array_map(function (Contract $contract) {
            return $contract->getName();
        }, $contracts);
    }

    /**
     * Compares
     * @param Scheme[] $schemes1
     * @param Scheme[] $schemes2
     * @return bool
     */
    private function compareSchemaQueue(array $schemes1, array $schemes2): bool
    {
        return true;
    }
}