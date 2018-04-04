<?php
namespace App\Service\SchemeService;

use App\Service\SchemeStorage\SchemeStorageInterface;
use App\Service\SchemeStorage\Models\Contract as ContractModel;

/**
 * Interface SchemeServiceInterface
 * @package App\Service\SchemeService
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
interface SchemeServiceInterface
{
    /**
     * @return SchemeStorageInterface
     */
    public function getStorage(): SchemeStorageInterface;

    /**
     * Register new service with scheme
     *
     * @param array $schemes
     * @param array $service
     * @return bool
     */
    public function registerContract(array $schemes, array $service): bool;

    /**
     * Register new service using scheme
     *
     * @param array $schemes
     * @param array $service
     * @return bool
     */
    public function registerUsage(array $schemes, array $service): bool;

    /**
     * @return ContractModel[]
     */
    public function getAll(): array;

    /**
     * @param array $schemes
     * @return ContractModel|null
     */
    public function get(array $schemes);
    /**
     * Remove contract
     * @param array $schemes
     * @param array $service
     * @return bool
     */
    public function removeContract(array $schemes, array $service): bool;

    /**
     * Remove usage of contract
     * @param array $schemes
     * @param array $service
     * @return bool
     */
    public function removeUsage(array $schemes, array $service): bool;

    /**
     * Returns contract without services, but with usages
     * @return ContractModel[]|array
     */
    public function getProblems(): array;

    /**
     * Return suitable contracts for passed schemes
     * @param array $schemes
     * @return ContractModel[]
     */
    public function resolve(array $schemes): array;

    /**
     * Check is service removing breaks contract usage
     * @param array $schemes
     * @param array $service
     * @return bool
     */
    public function isRemoval(array $schemes, array $service): bool;
}
