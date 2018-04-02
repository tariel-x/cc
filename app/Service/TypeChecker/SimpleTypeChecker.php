<?php
namespace App\Service\TypeChecker;

class SimpleTypeChecker implements TypeCheckerInterface
{
    public function compare(array $scheme1, array $scheme2): bool
    {
        return $this->compareSomething($scheme1, $scheme2);
    }

    public function compareSomething(array $scheme1, array $scheme2): bool
    {
        switch ($scheme2['type']) {
            case "object":
                return $this->checkObject($scheme1, $scheme2);
                reset;
            default:
                return $this->checkScalar($scheme1, $scheme2);
        };
    }

    private function checkObject(array $obj1, array $obj2): bool
    {
        $result = $this->checkObjectRequired($obj1, $obj2);
        if ($result === false) {
            return false;
        }
        foreach ($obj2['properties'] as $key => $prop2) {
            $prop1 = empty($obj1[$key]) ? null : $obj1[$key];
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

    private function checkObjectRequired(array $obj1, array $obj2): bool
    {
        //todo if obj1 reqs is empty - true
        //if obj1 reqs in obj2 reqs - true
        //if obj1 reqs eq obj2 reqs - true
        //otherwise - false
        return true;
    }

    private function checkScalar(array $slr1, array $slr2): bool
    {
        //todo check types
        //check format
        //check regexp for string
        //other
        return true;
    }
}
