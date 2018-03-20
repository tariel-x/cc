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

    public function save(array $scheme, string $name, array $service): bool
    {
        $this->schemes[$name] = [
            'scheme' => $scheme,
            'service' => $service,
        ];
        return true;
    }

    public function get(string $name): ?array
    {
        if (array_key_exists($name, $this->schemes)) {
            return $this->schemes[$name]['scheme'];
        }
        return null;
    }

    public function getService(string $name): ?array
    {
        if (array_key_exists($name, $this->schemes)) {
            return $this->schemes[$name]['service'];
        }
        return null;
    }  
}