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

    public function register(array $scheme, string $name): bool
    {
        return $this->getService()->register($scheme, [], $name);
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