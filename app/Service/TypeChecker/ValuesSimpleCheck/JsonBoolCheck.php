<?php
namespace App\Service\TypeChecker\ValuesSimpleCheck;

/**
 * Class JsonBoolCheck
 * @package App\Service\TypeChecker\ValuesSimpleCheck
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class JsonBoolCheck extends JsonCommonCheck
{
    /**
     * Compare bool field definitions
     * @param array $scheme1
     * @param array $scheme2
     * @return bool
     */
    public function compare(array $scheme1, array $scheme2): bool
    {
        return parent::compare($scheme1, $scheme2);
    }
}
