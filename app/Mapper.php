<?php

namespace App;

class Mapper
{
    public function map(array $data, $object)
    {
        $result = false;
        foreach (get_object_vars($object) as $key=>$item) {
            if (array_key_exists($key, $data)) {
                $object->{$key} = $data[$key];
                $result = true;
            }
        }
        return $result;
    }
}