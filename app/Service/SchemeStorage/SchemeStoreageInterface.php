<?php
namespace App\Service\SchemeStorage;

interface SchemeStoreageInterface
{
    /**
     * save the scheme
     *
     * @param array $scheme
     * @param string $name
     * @param array $service
     * @return boolean
     */
    public function save(array $scheme, string $name, array $service): bool;

    public function get(string $name): array;

    public function getService(string $name): array;
}