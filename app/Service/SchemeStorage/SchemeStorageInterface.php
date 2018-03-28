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
    public function saveContract(Contract $contract): bool;

    /**
     * Get all schemes
     *
     * @return Contract[]
     */
    public function getAllContracts(): array;

    /**
     * Get contract by scheme
     *
     * @param array $schemes
     * @return Contract
     */
    public function getContract(array $schemes): ?Contract;

    /**
     * Remove contract
     *
     * @param array $schemes
     * @return boolean
     */
    public function removeContract(array $schemes): bool;
}
