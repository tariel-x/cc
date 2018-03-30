<?php

namespace App\Service\SchemeService\Models;

class Service implements \JsonSerializable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $address;

    /**
     * @var string
     */
    private $checkUrl;

    /**
     * Service constructor.
     * @param string $name
     * @param array $address
     * @param string $checkUrl
     */
    public function __construct(string $name, array $address, string $checkUrl)
    {
        $this->name = $name;
        $this->address = $address;
        $this->checkUrl = $checkUrl;
    }    

    public function jsonSerialize()
    {
        return [
            'name'     => $this->getName(),
            'address'  => $this->getAddress(),
            'check_url' => $this->getCheckUrl(),
        ];
    }

    /**
     * Get the value of address
     *
     * @return  array
     */ 
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Get the value of name
     *
     * @return  string
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCheckUrl(): string
    {
        return $this->checkUrl;
    }
}