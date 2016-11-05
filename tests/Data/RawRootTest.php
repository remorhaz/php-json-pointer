<?php

namespace Remorhaz\JSONPointer\Test\Data;

use Remorhaz\JSONPointer\Data\Raw;

class RawRootTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param mixed $data
     * @dataProvider providerRootData
     */
    public function testCorrectDataAfterCreation($data)
    {
        $actualData = (new Raw($data))->getData();
        $this->assertEquals($data, $actualData);
    }


    /**
     * @param mixed $data
     * @dataProvider providerRootData
     */
    public function testHasDataAfterCreation($data)
    {
        $hasData = (new Raw($data))->hasData();
        $this->assertTrue($hasData);
    }


    public function providerRootData(): array
    {
        return [
            'scalarData' => [1],
            'structData' => [[0, 1]],
        ];
    }


    /**
     * @param mixed $sourceData
     * @param mixed $targetData
     * @dataProvider providerReplaceData
     */
    public function testCorrectDataAfterReplace($sourceData, $targetData)
    {
        $expectedData = $sourceData;
        $sourceRaw = new Raw($sourceData);
        $actualData = (new Raw($targetData))->replaceData($sourceRaw)->getData();
        $this->assertEquals($expectedData, $actualData);
    }


    /**
     * @param mixed $sourceData
     * @param mixed $targetData
     * @dataProvider providerReplaceData
     */
    public function testHasDataAfterReplace($sourceData, $targetData)
    {
        $sourceRaw = new Raw($sourceData);
        $hasData = (new Raw($targetData))->replaceData($sourceRaw)->hasData();
        $this->assertTrue($hasData);
    }


    public function providerReplaceData(): array
    {
        return [
            'scalarData' => [1, 'abc'],
            'structData' => [[0, 1], (object) ['a' => 'b']],
        ];
    }
}