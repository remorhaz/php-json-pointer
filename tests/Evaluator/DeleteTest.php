<?php

namespace Remorhaz\JSONPointer\Test\Evaluator;

use Remorhaz\JSONPointer\Parser\Parser;
use Remorhaz\JSONPointer\Evaluator\LocatorEvaluatorDelete;

class DeleteTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $expectedData
     * @dataProvider providerValidLocator
     */
    public function testDeleteDataWithValidLocator($text, $data, $expectedData)
    {
        $locator = Parser::factory()
            ->setText($text)
            ->getLocator();
        LocatorEvaluatorDelete::factory()
            ->setData($data)
            ->setLocator($locator)
            ->evaluate();
        $this->assertEquals($expectedData, $data, "Incorrect data after deleting");
    }


    public function providerValidLocator()
    {
        return [
            'rootProperty' => ['/a', (object) ['a' => 1, 'b' => 2], (object) ['b' => 2]],
            'nestedProperty' => [
                '/a/b',
                (object) ['a' => (object) ['b' => 1, 'c' => 2], 'd' => 3],
                (object) ['a' => (object) ['c' => 2], 'd' => 3]
            ],
            'lastRootIndex' => ['/0', [1], []],
            'notLastRootIndex' => ['/1', [1, 2], [1]],
        ];
    }
}
