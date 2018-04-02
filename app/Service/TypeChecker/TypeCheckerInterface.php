<?php
namespace App\Service\TypeChecker;

/**
 * Interface SchemeCheckerInterface
 * 
 * @package App\Service\SchemeChecker
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
interface TypeCheckerInterface
{
    /**
     * Compare that scheme2 is compatible with scheme1
     * @param array $schemes1
     * @param array $schemes2
     * @return bool
     */
    public function compare(array $scheme1, array $scheme2): bool;
}