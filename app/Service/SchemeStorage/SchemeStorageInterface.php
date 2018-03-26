<?php
namespace App\Service\SchemeStorage;

use App\Service\SchemeStorage\Models\Contract;

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
     * Get all schemes
     *
     * @return Contract[]
     */
    public function getAll(): array;

    /**
     * Remove schemes by name
     *
     * @param string $name
     * @return bool
     */
    public function removeContracts(string $name): bool;
}
