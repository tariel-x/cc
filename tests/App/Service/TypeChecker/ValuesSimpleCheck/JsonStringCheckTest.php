<?php

namespace tests\App\Service\TypeChecker\ValuesSimpleCheck;

use App\Service\TypeChecker\ValuesSimpleCheck\JsonStringCheck;
use Codeception\Stub;
use PHPUnit\Framework\TestCase;

/**
 * Class JsonStringCheckTest
 * @package tests\App\Service\TypeChecker\ValuesSimpleCheck
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class JsonStringCheckTest extends TestCase
{
    /**
     * @var JsonStringCheck
     */
    private $checker;

    protected function setUp()
    {
        $this->checker = Stub::make(JsonStringCheck::class);
        parent::setUp();
    }

    public function testCompare()
    {
        $sc1 = [
            'type' => 'string',
        ];
        $sc2 = [
            'type' => 'string',
        ];
        $this->assertTrue($this->checker->compare($sc1, $sc2));
    }

    public function testCompareConstraints()
    {
        $sc1 = [
            'type' => 'string',
        ];
        $sc2 = [
            'type'      => 'string',
            'maxLength' => 50,
        ];
        $this->assertTrue($this->checker->compare($sc1, $sc2));
    }

    public function testCompareConstraintsEqual()
    {
        $sc1 = [
            'type' => 'string',
            'maxLength' => 50,
        ];
        $sc2 = [
            'type'      => 'string',
            'maxLength' => 50,
        ];
        $this->assertTrue($this->checker->compare($sc1, $sc2));
    }

    public function testCompareConstraintsNEqual()
    {
        $sc1 = [
            'type' => 'string',
            'maxLength' => 70,
        ];
        $sc2 = [
            'type'      => 'string',
            'maxLength' => 50,
        ];
        $this->assertFalse($this->checker->compare($sc1, $sc2));
    }

    public function testCompareParentConstraints()
    {
        $sc1 = [
            'type'      => 'string',
            'maxLength' => 70,
        ];
        $sc2 = [
            'type' => 'string',
        ];
        $this->assertFalse($this->checker->compare($sc1, $sc2));
    }

    public function testCompareUnknownConstraints()
    {
        $sc1 = [
            'type'   => 'string',
            'blabla' => 70,
        ];
        $sc2 = [
            'type'  => 'string',
            'ololo' => false,
        ];
        $this->assertTrue($this->checker->compare($sc1, $sc2));
    }
}