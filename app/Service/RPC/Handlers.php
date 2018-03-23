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
     * @param string $name
     * @return bool
     */
    public function register(array $scheme, array $service, string $name): bool
    {
        return $this->getService()->register($scheme, $service, $name);
    }

    public function getSchemes(string $name): array
    {
        return $this->getService()->getContracts($name);
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