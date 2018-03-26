<?php
namespace App\Service\SchemeService;

/**
 * Class Contract
 * @package App\Service\SchemeStorage
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class Contract implements \JsonSerializable
{
    /**
     * @var array
     */
    private $schemes;

    /**
     * @var Service
     */
    private $service;

    /**
     * Contract constructor.
     *
     * @param array $schemes
     * @param array $service
     */
    public function __construct(array $schemes, Service $service)
    {
        $this->schemes = $schemes;
        $this->service = $service;
    }

    /**
     * @return array
     */
    public function getSchemes(): array
    {
        return $this->schemes;
    }

    /**
     * @param array $schemes
     * @return Contract
     */
    public function setSchemes(array $schemes): Contract
    {
        $this->schemes = $schemes;
        return $this;
    }

    /**
     * @return Service
     */
    public function getService(): Service
    {
        return $this->service;
    }

    /**
     * @param Service $service
     * @return Contract
     */
    public function setService(Service $service): Contract
    {
        $this->service = $service;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'schemes' => $this->getSchemes(),
            'service' => $this->getService(),
        ];
    }
}
