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
     * Save scheme
     * @param Contract $contract
     * @return bool
     */
    public function save(Contract $contract): bool;

    /**
     * Get schemes by name
     *
     * @param string $name
     * @return Contract[]
     */
    public function get(string $name): array;

    /**
     * Get all schemes
     *
     * @return Contract[]
     */
    public function getAll(): array;

    /**
     * Get names by scheme
     *
     * @param array $scheme
     * @return string[]
     */
    public function getNameByContract(array $scheme): array;

    /**
     * Remove schemes by name
     *
     * @param string $name
     * @return bool
     */
    public function removeContracts(string $name): bool;
}
