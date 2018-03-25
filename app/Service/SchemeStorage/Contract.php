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
    private $scheme;

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
    public function getScheme(): array
    {
        return $this->scheme;
    }

    /**
     * @param array $scheme
     * @return Contract
     */
    public function setScheme(array $scheme): Contract
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * @return array
     */
    public function getService(): array
    {
        return $this->service;
    }

    /**
     * @param array $service
     * @return Contract
     */
    public function setService(array $service): Contract
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
