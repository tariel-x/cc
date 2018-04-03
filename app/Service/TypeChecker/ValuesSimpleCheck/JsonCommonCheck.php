<?php

namespace App\Service\TypeChecker\ValuesSimpleCheck;

use App\Service\TypeChecker\TypeCheckerInterface;

class JsonCommonCheck implements TypeCheckerInterface
{
    public function compare(array $scheme1, array $scheme2): bool
    {
        if (empty($scheme1['type']) || empty($scheme2['type'])) {
            return false;
        }
        if ($scheme2['type'] !== $scheme1['type']) {
            return false;
        }
        return true;
    }
}