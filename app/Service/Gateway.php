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
        $this->client = new Client([
            'base_uri' => $address,
            'timeout'  => 2.0,
        ]);
    }

    public function addPyramid(Pyramid $model)
    {
        $this->mapFields($model);
        return true;
    }

    private function mapFields(Pyramid $model)
    {
        $ids = [];
        foreach (get_object_vars($model) as $key=>$item) {
            $matches = [];
            preg_match('/row(\d+)col(\d+)/', $key, $matches);
            if (count($matches) == 3) {
                $ids[] = $this->createRecord((int)$matches[2], (int)$matches[1], (int)$model->{$key}, $model->full_name, $model->class_name);
            }
        }
        return $ids;
    }

    private function createRecord(int $col, int $row, int $val, string $full_name, string $class_name)
    {
        $response = $this->client->request('POST', '/record', [
            'json' => [
                'full_name' => $full_name,
                'class_name' => $class_name,
                'col' => $col,
                'row' => $row,
                'val' => $val,
            ],
        ]);
        $data = $response->getBody();
        $model = json_decode($data, true);
        if (!array_key_exists('id', $model)) {
            throw new \Exception('No id key');
        }
        return $model['id'];
    }

    public function getStat(string $class_name)
    {
        $response = $this->client->request('GET', '/stat', [
            'query' => [
                'name' => $class_name,
            ],
        ]);
        $data = $response->getBody();
        $model = json_decode($data, true);
        return $model;
    }
}