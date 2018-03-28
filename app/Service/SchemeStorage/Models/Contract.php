<?php
namespace App\Service\SchemeStorage\Models;

/**
 * Class Contract
 * @package App\Service\SchemeStorage\Model
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class Contract implements \JsonSerializable
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var array
     */
    private $schemes;

    /**
     * @var array
     */
    private $services = [];

    /**
     * @var array
     */
    private $usages = [];

    /**
     * Contract constructor.
     *
     * @param array $scheme
     * @param array $services
     */
    public function __construct(array $schemes)
    {
        $this->schemes = $schemes;
    }

    /**
     * @return array
     */
    public function getSchemes(): array
    {
        return $this->schemes;
    }

    /**
     * @param array $scheme
     * @return Contract
     */
    public function setSchemes(array $schemes): Contract
    {
        $this->schemes = $schemes;
        return $this;
    }

    /**
     * @return array
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @param array $services
     * @return Contract
     */
    public function setServices(array $services): Contract
    {
        $this->services = $services;
        return $this;
    }

    /**
     * Append service to services list
     *
     * @param array $service
     * @return Contract
     */
    public function addService(array $service): Contract
    {
        $this->services[] = $service;
        return $this;
    }

    public function getUsages(): array
    {
        return $this->usages;
    }

    public function setUsages(array $usages): Contract
    {
        $this->usages = $usages;
        return $this;
    }

    public function addUsage(array $usage): Contract
    {
        $this->usages[] = $usage;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'schemes'  => $this->getSchemes(),
            'services' => $this->getServices(),
            'usages'   => $this->getUsages(),
        ];
    }
}
