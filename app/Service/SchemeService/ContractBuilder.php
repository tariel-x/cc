<?php

namespace App\Service\SchemeService;

class ContractBuilder
{
    public function buildContract(array $schemes, array $service): Contract
    {
        return (new Contract($this->makeSchemes($schemes)))
            ->setService($this->makeService($service));
    }

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
        return new Service($service['name'], $service['address']);
    }
}
