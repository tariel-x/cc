<?php
namespace tests\App\Service\TypeChecker;

use App\Service\TypeChecker\SimpleTypeChecker;
use Codeception\Stub;
use PHPUnit\Framework\TestCase;

/**
 * Class SimpleTypeCheckerTest
 * @package tests\App\Service\TypeChecker
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class SimpleTypeCheckerTest extends TestCase
{
    /**
     * @var SimpleTypeChecker
     */
    private $checker;

    protected function setUp()
    {
        $this->checker = Stub::make(SimpleTypeChecker::class);
        parent::setUp();
    }

    public function testCompareEmpty()
    {
        $sc1 = [];
        $sc2 = [];
        $this->assertTrue($this->checker->compare($sc1, $sc2));
    }

    public function testCompareObject()
    {
        $sc1 = [
            'type' => 'object',
            'properties' => [
                'f1' => [
                    'type' => 'string',
                ],
            ],
        ];
        $sc2 = [
            'type' => 'object',
            'properties' => [
                'f1' => [
                    'type' => 'string',
                ],
            ],
        ];
        $this->assertTrue($this->checker->compare($sc1, $sc2));
    }

    public function testCompareObjectDiff()
    {
        $sc1 = [
            'type' => 'object',
            'properties' => [
                'f1' => [
                    'type' => 'string',
                ],
            ],
        ];
        $sc2 = [
            'type' => 'object',
            'properties' => [
                'f2' => [
                    'type' => 'string',
                ],
            ],
        ];
        $this->assertFalse($this->checker->compare($sc1, $sc2));
    }

    public function testCompareObjectReqEmpty()
    {
        $sc1 = [
            'type' => 'object',
            'properties' => [],
            'required' => [],
        ];
        $sc2 = [
            'type' => 'object',
            'properties' => [],
            'required' => ['f1'],
        ];
        $this->assertTrue($this->checker->compare($sc1, $sc2));
    }

    public function testCompareObjectReqDiff()
    {
        $sc1 = [
            'type' => 'object',
            'properties' => [],
            'required' => ['f2'],
        ];
        $sc2 = [
            'type' => 'object',
            'properties' => [],
            'required' => ['f1'],
        ];
        $this->assertFalse($this->checker->compare($sc1, $sc2));
    }

    public function testCompareObjectReqDiff2()
    {
        $sc1 = [
            'type' => 'object',
            'properties' => [],
            'required' => ['f2'],
        ];
        $sc2 = [
            'type' => 'object',
            'properties' => [],
            'required' => [],
        ];
        $this->assertFalse($this->checker->compare($sc1, $sc2));
    }

    public function testCompareObjectReqIn()
    {
        $sc1 = [
            'type' => 'object',
            'properties' => [],
            'required' => ['f1'],
        ];
        $sc2 = [
            'type' => 'object',
            'properties' => [],
            'required' => ['f1', 'f2'],
        ];
        $this->assertTrue($this->checker->compare($sc1, $sc2));
    }

    public function testCompareArr()
    {
        $sc1 = [
            'type'  => 'array',
            'items' => [
                'type' => 'integer'
            ],
        ];
        $sc2 = [
            'type'  => 'array',
            'items' => [
                'type' => 'integer'
            ],
        ];
        $this->assertTrue($this->checker->compare($sc1, $sc2));
    }

    public function testCompareArrItemsEmpty()
    {
        $sc1 = [
            'type'  => 'array',
            'items' => [],
        ];
        $sc2 = [
            'type'  => 'array',
            'items' => [],
        ];
        $this->assertFalse($this->checker->compare($sc1, $sc2));
    }

    public function testCompareArrItemsDiff()
    {
        $sc1 = [
            'type'  => 'array',
            'items' => [
                'type' => 'string'
            ],
        ];
        $sc2 = [
            'type'  => 'array',
            'items' => [
                'type' => 'integer'
            ],
        ];
        $this->assertFalse($this->checker->compare($sc1, $sc2));
    }

    public function testCompareRecursion()
    {
        $sc1 = [
            'type' => 'object',
            'properties' => [
                'f1' => [
                    'type'  => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'f2' => [
                                'type' => 'string',
                            ],
                        ],
                        'required' => ['f2'],
                    ],
                ],
            ],
            'required' => ['f1'],
        ];
        $sc2 = [
            'type' => 'object',
            'properties' => [
                'f1' => [
                    'type'  => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'f2' => [
                                'type' => 'string',
                            ],
                        ],
                        'required' => ['f2'],
                    ],
                ],
            ],
            'required' => ['f1'],
        ];
        $this->assertTrue($this->checker->compare($sc1, $sc2));
    }
}