<?php
namespace App\Service\SchemeChecker;

/**
 * Interface SchemeCheckerInterface
 * @package App\Service\SchemeChecker
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
interface SchemeCheckerInterface
{
    /**
     * Compare that schemes2 is compatible with schemes1
     * @param array $schemes1
     * @param array $schemes2
     * @return bool
     */
    public function schemeCompatible(array $schemes1, array $schemes2): bool;
}