<?php
namespace App\Service\TypeChecker\ValuesSimpleCheck;

/**
 * Class JsonIntegerCheck
 * @package App\Service\TypeChecker\ValuesSimpleCheck
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class JsonIntegerCheck extends JsonCommonCheck
{
    /**
     * Compare numeric field definitions
     * @param array $scheme1
     * @param array $scheme2
     * @return bool
     */
    public function compare(array $scheme1, array $scheme2): bool
    {
        $result = parent::compare($scheme1, $scheme2);
        if ($result === false) {
            return false;
        }
        return $this->compareValidationProps($scheme1, $scheme2, [
            'multipleOf',
            'maximum',
            'exclusiveMaximum',
            'minimum',
            'exclusiveMinimum',
        ]);
    }
}
