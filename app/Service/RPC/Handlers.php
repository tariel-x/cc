<?php
namespace App\Service\RPC;

use App\Service\SchemeService\SchemeServiceInterface;

/**
 * Class Handlers
 * @package App\Service\RPC
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class Handlers
{
    /**
     * @var SchemeServiceInterface
     */
    private $service;

    /**
     * Handlers constructor.
     * @param SchemeServiceInterface $service
     */
    public function __construct(SchemeServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Get the value of service
     *
     * @return SchemeServiceInterface
     */ 
    public function getService(): SchemeServiceInterface
    {
        return $this->service;
    }

    /**
     * status function
     *
     * @return array
     */
    public function status(): array
    {
        return [
            'status' => 'ok',
        ];
    }

    /**
     * Register contract
     * @param array $schemes
     * @param array $service
     * @return bool
     */
    public function registerContract(array $schemes, array $service): bool
    {
        return $this->getService()->registerContract($schemes, $service);
    }

    /**
     * Remove contract
     * @param array $schemes
     * @param array $service
     * @return bool
     */
    public function removeContract(array $schemes, array $service): bool
    {
        return $this->getService()->removeContract($schemes, $service);
    }

    /**
     * Get all contracts
     * @return array
     */
    public function getAll(): array
    {
        return $this->getService()->getAll();
    }

    /**
     * Get specific contract
     * @param array $schemes
     * @return \App\Service\SchemeStorage\Models\Contract|null
     */
    public function get(array $schemes)
    {
        return $this->getService()->get($schemes);
    }

    /**
     * Register usage of contract
     * @param array $schemes
     * @param array $usage
     * @return bool
     */
    public function registerUsage(array $schemes, array $usage): bool
    {
        return $this->getService()->registerUsage($schemes, $usage);
    }

    /**
     * Remove usage of contract
     * @param array $schemes
     * @param array $usage
     * @return bool
     */
    public function removeUsage(array $schemes, array $usage): bool
    {
        return $this->getService()->removeUsage($schemes, $usage);
    }

    /**
     * Get contracts without services and with usages
     * @return \App\Service\SchemeStorage\Models\Contract[]|array
     */
    public function getProblems()
    {
        return $this->getService()->getProblems();
    }

    /**
     * Get suitable contracts
     * @param array $schemes
     * @return array
     */
    public function resolve(array $schemes): array
    {
        return $this->getService()->resolve($schemes);
    }

    /**
     * Check is service removing breaks contract usage
     * @param array $schemes
     * @param array $service
     * @return bool
     */
    public function isRemoval(array $schemes, array $service): bool
    {
        return $this->getService()->isRemoval($schemes, $service);
    }
}
