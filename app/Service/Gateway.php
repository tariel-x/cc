<?php

namespace App\Service;

use App\Model\Pyramid;
use GuzzleHttp\Client;

class Gateway
{
    private $address;
    private $port;
    private $client;

    public function __construct(string $address)
    {
        $this->address = $address;
        $this->port = $port;
        $this->client = new Client([
            'base_uri' => $address,
            'timeout'  => 2.0,
        ]);
    }

    public function addPyramid(Pyramid $model)
    {
        $pyramidId = $this->createPyramid($model->full_name, $model->class_name);
        $this->mapFields($model, $pyramidId);
        return true;
    }

    private function mapFields(Pyramid $model, int $id)
    {

    }

    private function createPyramid(string $full_name, string $class_name)
    {
        return 0;
    }

    private function createRecord(string $class_name, int $col, int $row, int $val)
    {
        
    }
}