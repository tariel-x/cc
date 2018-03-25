<?php
namespace App\Service\SchemeStorage;

/**
 * Class Contract
 * @package App\Service\SchemeStorage
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class Contract implements \JsonSerializable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $schemes;

    /**
     * @var array
     */
    private $service;

    /**
     * Contract constructor.
     *
     * @param string $name
     * @param array $scheme
     * @param array $service
     */
    public function __construct(string $name, array $scheme, array $service)
    {
        $this->name = $name;
        $this->scheme = $scheme;
        $this->service = $service;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Contract
     */
    public function setName(string $name): Contract
    {
        $this->name = $name;
        return $this;
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
            'name' => $this->getName(),
            'schemes' => $this->getScheme(),
            'service' => $this->getService(),
        ];
    }
}
