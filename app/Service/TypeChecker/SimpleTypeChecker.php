<?php
namespace App\Service\TypeChecker;

use App\Service\TypeChecker\ValuesSimpleCheck\JsonBoolCheck;
use App\Service\TypeChecker\ValuesSimpleCheck\JsonCommonCheck;
use App\Service\TypeChecker\ValuesSimpleCheck\JsonIntegerCheck;
use App\Service\TypeChecker\ValuesSimpleCheck\JsonStringCheck;

/**
 * Class SimpleTypeChecker
 * @package App\Service\TypeChecker
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class SimpleTypeChecker implements TypeCheckerInterface
{
    /**
     * Compare that scheme2 is compatible with scheme1
     * @param array $scheme1
     * @param array $scheme2
     * @return bool
     */
    public function compare(array $scheme1, array $scheme2): bool
    {
        return $this->compareSomething($scheme1, $scheme2);
    }

    /**
     * Select type - object, scalar or array and call specific methods
     * @param array $scheme1
     * @param array $scheme2
     * @return bool
     */
    private function compareSomething(array $scheme1, array $scheme2): bool
    {
        if (empty($scheme1['type']) && empty($scheme2['type'])) {
            return true;
        }
        if (empty($scheme2['type'])) {
            return false;
        }
        switch ($scheme2['type']) {
            case 'object':
                return $this->checkObject($scheme1, $scheme2);
            case 'array':
                return $this->checkArray($scheme1, $scheme2);
            default:
                return $this->checkScalar($scheme1, $scheme2);
        }
    }

    /**
     * Checks object type value
     * @param array $obj1
     * @param array $obj2
     * @return bool
     */
    private function checkObject(array $obj1, array $obj2): bool
    {
        $result = $this->checkObjectRequired($obj1, $obj2);
        if ($result === false) {
            return false;
        }
        foreach ($obj2['properties'] as $key => $prop2) {
            $prop1 = empty($obj1['properties'][$key]) ? null : $obj1['properties'][$key];
            if ($prop1 === null) {
                return false;
            }
            $result = $this->compareSomething($prop1, $prop2);
            if ($result === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks required section for object type value
     * @param array $obj1
     * @param array $obj2
     * @return bool
     */
    private function checkObjectRequired(array $obj1, array $obj2): bool
    {
        //if obj1 reqs is empty - true
        if (empty($obj1['required'])) {
            return true;
        }

        //if obj1 reqs eq obj2 reqs - true
        $diff = array_diff($obj1['required'], $obj2['required']);
        if (count($diff) === 0) {
            return true;
        }

        //if obj1 reqs in obj2 reqs - true
        $intersect = array_intersect($obj1['required'], $obj2['required']);
        $diff = array_diff($obj2['required'], $intersect);
        if (!empty($intersect) && count($diff) === 0) {
            return true;
        }
        return false;
    }

    /**
     * Checks definitions of scalar field
     * @param array $slr1
     * @param array $slr2
     * @return bool
     */
    private function checkScalar(array $slr1, array $slr2): bool
    {
        switch ($slr2['type']) {
            case 'string':
                return (new JsonStringCheck())->compare($slr1, $slr2);
            case 'integer':
                return (new JsonIntegerCheck())->compare($slr1, $slr2);
            case 'number':
                return (new JsonIntegerCheck())->compare($slr1, $slr2);
            case 'bool':
                return (new JsonBoolCheck())->compare($slr1, $slr2);
            default:
                return (new JsonCommonCheck())->compare($slr1, $slr2);
        }
    }

    /**
     * Passes items object to compare
     * @param array $arr1
     * @param array $arr2
     * @return bool
     */
    private function checkArray(array $arr1, array $arr2): bool
    {
        if (empty($arr1['items']) || empty($arr2['items'])) {
            return false;
        }
        return $this->compareSomething($arr1['items'], $arr2['items']);
    }
}
