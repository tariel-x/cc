<?php

namespace App\Service;

class Helper
{
    public function sort(&$data)
    {
        if (!is_array($data)) {
            return;
        }
        foreach ($data as &$value) {
            $this->sort($value);
        }
        ksort($data);
    }
}