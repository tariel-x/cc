<?php
namespace App\Service\TypeChecker\ValuesSimpleCheck;

use App\Service\TypeChecker\TypeCheckerInterface;

/**
 * Class JsonCommonCheck
 * @package App\Service\TypeChecker\ValuesSimpleCheck
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
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
        return $this->compareValidationProps($scheme1, $scheme2, [
            'format',
        ]);
    }

    /**
     * @param array $scheme1
     * @param array $scheme2
     * @param string[] $props
     * @return bool
     */
    protected function compareValidationProps(array $scheme1, array $scheme2, array $props): bool
    {
        return array_reduce($props, function (bool $carry, string $prop) use ($scheme1, $scheme2) {
            return $carry && $this->compareValidationProp($scheme1, $scheme2, $prop);
        }, true);
    }

    /**
     * Checks that scheme1 doesn't contain such prop definition (doesn't make such constraints) or both constraints are equal
     * @param array $scheme1
     * @param array $scheme2
     * @param string $prop
     * @return bool
     */
    protected function compareValidationProp(array $scheme1, array $scheme2, string $prop): bool
    {
        if (!empty($scheme1[$prop])
            && $scheme1[$prop] !== $scheme2[$prop]) {
            return false;
        }
        return true;
    }
}
