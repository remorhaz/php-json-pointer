<?php

namespace Remorhaz\JSONPointer\Test;

use Remorhaz\JSONPointer\Pointer\Locator\Reference;
use Remorhaz\JSONPointer\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @expectedException \Remorhaz\JSONPointer\Parser\Exception
     */
    public function testUninitializedLocatorAccessThrowsException()
    {
        Parser::factory()->getLocator();
    }


    /**
     * @expectedException \RuntimeException
     */
    public function testUninitializedLocatorAccessThrowsSplException()
    {
        Parser::factory()->getLocator();
    }


    /**
     * @param string $text
     * @param array $referenceList
     * @dataProvider providerReferenceList
     */
    public function testLocatorReferenceList($text, array $referenceList)
    {
        $parser = Parser::factory()->setText($text);
        $actualReferenceList = $parser->getLocator()->getReferenceList();
        $this->assertCount(count($referenceList), $actualReferenceList, "Incorrect count of references");
        foreach ($actualReferenceList as $index => $actualReference) {
            list($expectedType, $expectedValue) = array_shift($referenceList);
            $this->assertEquals($expectedType, $actualReference->getType(), "Incorrect type in reference #{$index}");
            $this->assertEquals($expectedValue, $actualReference->getKey(), "Incorrect value in reference #{$index}");
        }
    }


    public function providerReferenceList()
    {
        return [
            'noReferences' => ['', []],
            'singleEmptyReference' => [
                '/',
                [
                    [Reference::TYPE_PROPERTY, '']
                ],
            ],
            'singleLatinReference' => [
                '/abc',
                [
                    [Reference::TYPE_PROPERTY, 'abc'],
                ],
            ],
            'singleCyrillicReference' => [
                '/абв',
                [
                    [Reference::TYPE_PROPERTY, 'абв'],
                ],
            ],
            'singleKanjiReference' => [
                '/日本語',
                [
                    [Reference::TYPE_PROPERTY, '日本語'],
                ],
            ],
            'multipleEmptyReferences' => [
                '///',
                [
                    [Reference::TYPE_PROPERTY, ''],
                    [Reference::TYPE_PROPERTY, ''],
                    [Reference::TYPE_PROPERTY, ''],
                ],
            ],
            'singleZero' => [
                '/0',
                [
                    [Reference::TYPE_INDEX, '0'],
                ],
            ],
            'singleNumber' => [
                '/6',
                [
                    [Reference::TYPE_INDEX, '6'],
                ],
            ],
            'singleZeroPrefixedNumber' => [
                '/06',
                [
                    [Reference::TYPE_PROPERTY, '06'],
                ],
            ],
            'singleDash' => [
                '/-',
                [
                    [Reference::TYPE_NEXT_INDEX, '-'],
                ],
            ],
            'finalDash' => [
                '/abc/-',
                [
                    [Reference::TYPE_PROPERTY, 'abc'],
                    [Reference::TYPE_NEXT_INDEX, '-'],
                ],
            ],
            'singleTilda' => [
                '/~0',
                [
                    [Reference::TYPE_PROPERTY, '~'],
                ],
            ],
            'singleSlash' => [
                '/~1',
                [
                    [Reference::TYPE_PROPERTY, '/'],
                ],
            ],
            'singlePartiallyEscapedReference' => [
                '/~01~10',
                [
                    [Reference::TYPE_PROPERTY, '~1/0'],
                ],
            ],
            'mixedReferences' => [
                "/\t/a/б~0в/~1/12/034/日本~1語",
                [
                    [Reference::TYPE_PROPERTY, "\t"],
                    [Reference::TYPE_PROPERTY, 'a'],
                    [Reference::TYPE_PROPERTY, 'б~в'],
                    [Reference::TYPE_PROPERTY, '/'],
                    [Reference::TYPE_INDEX, '12'],
                    [Reference::TYPE_PROPERTY, '034'],
                    [Reference::TYPE_PROPERTY, '日本/語'],
                ],
            ],
        ];
    }


    /**
     * @param string $text
     * @dataProvider providerSyntaxError
     * @expectedException \Remorhaz\JSONPointer\Parser\Exception
     */
    public function testSyntaxErrorThrowsException($text)
    {
        Parser::factory()
            ->setText($text)
            ->getLocator();
    }


    /**
     * @param string $text
     * @dataProvider providerSyntaxError
     * @expectedException \Remorhaz\JSONPointer\SyntaxException
     * @expectedExceptionMessageRegExp / at position #\d+/
     */
    public function testSyntaxErrorThrowsSyntaxException($text)
    {
        Parser::factory()
            ->setText($text)
            ->getLocator();
    }


    /**
     * @param string $text
     * @dataProvider providerSyntaxError
     * @expectedException \RuntimeException
     */
    public function testSyntaxErrorThrowsSplException($text)
    {
        Parser::factory()
            ->setText($text)
            ->getLocator();
    }


    public function providerSyntaxError()
    {
        return [
            'noStartingSlash' => ['abc'],
            'incompleteEscapeSequence' => ['/аб~в'],
            'invalidEscapeSequence' => ['/аб~2'],
        ];
    }


    /**
     * @param string $text
     * @param int $type
     * @dataProvider providerReferenceTypeDetection
     */
    public function testReferenceTypeDetection($text, $type)
    {
        $referenceList = Parser::factory()
            ->setText($text)
            ->getLocator()
            ->getReferenceList();
        $this->assertCount(1, $referenceList);
        $reference = $referenceList[0];
        $this->assertEquals($type, $reference->getType(), "Incorrect reference type detection");
    }


    public function providerReferenceTypeDetection()
    {
        return [
            'text' => ['/abc', Reference::TYPE_PROPERTY],
            'leadingZeroNumber' => ['/0123', Reference::TYPE_PROPERTY],
            'number' => ['/123', Reference::TYPE_INDEX],
            'dash' => ['/-', Reference::TYPE_NEXT_INDEX],
        ];
    }
}
