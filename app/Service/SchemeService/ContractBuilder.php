<?php

namespace App\Service\SchemeService;

use App\Service\SchemeService\Models\Contract;
use App\Service\SchemeService\Models\Scheme;
use App\Service\SchemeService\Models\Service;

/**
 * Class ContractBuilder
 * @package App\Service\SchemeService
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class ContractBuilder
{
    /**
     * Build contract with service from query
     * @param array $schemes
     * @param array $service
     * @return Contract
     */
    public function buildContract(array $schemes, array $service): Contract
    {
        return (new Contract($this->makeSchemes($schemes)))
            ->setService($this->makeService($service));
    }

    /**
     * Build contract with usage from query
     * @param array $schemes
     * @param array $service
     * @return Contract
     */
    public function buildUsageContract(array $schemes, array $service): Contract
    {
        return (new Contract($this->makeSchemes($schemes)))
            ->setUsage($this->makeService($service));
    }

     /**
     * @param array $schemes
     * @return Scheme[]
     */
    private function makeSchemes(array $schemes): array
    {
        return array_map(function (array $item) {
            return new Scheme(
                (bool)$item['in'],
                $item['scheme'],
                (string)$item['type']
            );
        }, $schemes);
    }

    private function makeService(array $service): Service
    {
        return new Service($service['name'], $service['address'], $service['check_url']);
    }
}
