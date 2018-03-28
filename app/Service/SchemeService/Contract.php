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
     * @var Service
     */
    private $usage;

    /**
     * Contract constructor.
     *
     * @param array $schemes
     * @param array $service
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
     * @param array $schemes
     * @return Contract
     */
    public function setSchemes(array $schemes): Contract
    {
        $this->schemes = $schemes;
        return $this;
    }

    /**
     * @return Service|null
     */
    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * @param Service|null $service
     * @return Contract
     */
    public function setService(?Service $service): Contract
    {
        $this->service = $service;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'schemes' => $this->getSchemes(),
            'service' => $this->getService(),
            'usage'   => $this->getUsage(),
        ];
    }

    /**
     * Get the value of usage
     *
     * @return  Service|null
     */ 
    public function getUsage(): ?Service
    {
        return $this->usage;
    }

    /**
     * Set the value of usage
     *
     * @param  Service|null  $usage
     *
     * @return  self
     */ 
    public function setUsage(?Service $usage): Contract
    {
        $this->usage = $usage;
        return $this;
    }
}
