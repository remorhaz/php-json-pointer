<?php

namespace Remorhaz\JSONPointer\Test\Data;

use Remorhaz\JSONPointer\Data\RawWriter;

class RootTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param mixed $data
     * @dataProvider providerRootData
     */
    public function testCorrectDataAfterCreation($data)
    {
        $actualData = (new RawWriter($data))->getData();
        $this->assertEquals($data, $actualData);
    }


    /**
     * @param mixed $data
     * @dataProvider providerRootData
     */
    public function testHasDataAfterCreation($data)
    {
        $hasData = (new RawWriter($data))->hasData();
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
        $sourceRaw = new RawWriter($sourceData);
        $actualData = (new RawWriter($targetData))->replaceData($sourceRaw)->getData();
        $this->assertEquals($expectedData, $actualData);
    }


    /**
     * @param mixed $sourceData
     * @param mixed $targetData
     * @dataProvider providerReplaceData
     */
    public function testHasDataAfterReplace($sourceData, $targetData)
    {
        $sourceRaw = new RawWriter($sourceData);
        $hasData = (new RawWriter($targetData))->replaceData($sourceRaw)->hasData();
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