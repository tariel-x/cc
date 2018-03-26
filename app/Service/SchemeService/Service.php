<?php

namespace App\Service\SchemeService;

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

    public function __construct(string $name, array $address)
    {
        $this->name = $name;
        $this->address = $address;
    }    

    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'address' => $this->getAddress(),
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
}