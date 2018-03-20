<?php
namespace App\Service\SchemeStorage;

/**
 * Interface SchemeStorageInterface
 * @package App\Service\SchemeStorage
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
interface SchemeStorageInterface
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

    /**
     * Get scheme by name
     *
     * @param string $name
     * @return array
     */
    public function get(string $name): ?array;

    /**
     * Get service info by scheme name
     *
     * @param string $name
     * @return array
     */
    public function getService(string $name): ?array;
}