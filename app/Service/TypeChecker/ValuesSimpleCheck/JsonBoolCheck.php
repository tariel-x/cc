<?php
namespace App\Service\TypeChecker\ValuesSimpleCheck;

/**
 * Class JsonBoolCheck
 * @package App\Service\TypeChecker\ValuesSimpleCheck
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class JsonBoolCheck extends JsonCommonCheck
{
    public function compare(array $scheme1, array $scheme2): bool
    {
        $result = parent::compare($scheme1, $scheme2);
        if ($result === false) {
            return false;
        }
        return true;
    }
}
