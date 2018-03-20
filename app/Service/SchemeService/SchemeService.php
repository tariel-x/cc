<?php
namespace App\Services\SchemeService;

use App\Service\SchemeStorage\Contract;
use App\Service\SchemeStorage\SchemeStorageInterface;

class SchemeService
{
    /**
     * @var SchemeStorageInterface
     */
    private $storage;

    /**
     * SchemeService constructor.
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
     * @param array $scheme
     * @param array $service
     * @param string $name
     * @return bool
     */
    public function register(array $scheme, array $service, string $name): bool
    {
        $contract = new Contract($name, $scheme, $service);
        return $this->getStorage()->save($contract);
    }
}