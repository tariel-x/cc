<?php

namespace App\Service\RPC;

use App\Service\SchemeService\SchemeService;

class Handlers
{
    /**
     * @var SchemeService
     */
    private $service;

    public function __construct(SchemeService $service)
    {
        $this->service = $service;
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
     * @param array $scheme
     * @param array $service
     * @return bool
     */
    public function register(array $scheme, array $service): bool
    {
        return $this->getService()->register($scheme, $service);
    }

    public function getAll(): array
    {
        return $this->getService()->getAll();
    }

    public function get(array $schemes)
    {
        return $this->getService()->get($schemes);
    }

    public function resolve(array $contract): array
    {
        return $this->getService()->resolveContract($contract);
    }

    /**
     * Get the value of service
     *
     * @return SchemeService
     */ 
    public function getService()
    {
        return $this->service;
    }
}