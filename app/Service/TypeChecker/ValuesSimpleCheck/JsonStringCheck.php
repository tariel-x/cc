<?php

namespace App\Service\TypeChecker\ValuesSimpleCheck;

class JsonStringCheck extends JsonCommonCheck
{
    public function compare(array $scheme1, array $scheme2): bool
    {
        $result = parent::compare($scheme1, $scheme2);
        if ($result === false) {
            return false;
        }
        if (!empty($scheme1['format'])
            && $scheme1['format'] !== $scheme2['format']) {
            return false;
        }
        return true;
    }
}