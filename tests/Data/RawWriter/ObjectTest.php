<?php

namespace Remorhaz\JSON\Test\Data\RawWriter;

use Remorhaz\JSON\Data\RawWriter;

class ObjectTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param \stdClass $data
     * @param string $property
     * @param mixed $value
     * @dataProvider providerExistingProperty
     */
    public function testCorrectDataAfterSelectingExistingProperty(\stdClass $data, string $property, $value)
    {
        $actualData = (new RawWriter($data))
            ->selectProperty($property)
            ->getData();
        $this->assertEquals($value, $actualData);
    }


    /**
     * @param \stdClass $data
     * @param string $property
     * @dataProvider providerExistingProperty
     */
    public function testHasDataAfterSelectingExistingProperty(\stdClass $data, string $property)
    {
        $hasData = (new RawWriter($data))
            ->selectProperty($property)
            ->hasData();
        $this->assertTrue($hasData);
    }


    /**
     * @param \stdClass $data
     * @param string $property
     * @param mixed $value
     * @dataProvider providerExistingProperty
     */
    public function testPropertyRemainsSelectedAfterRemovingExistingProperty(\stdClass $data, string $property, $value)
    {
        $expectedData = clone $data;
        $valueReader = new RawWriter($value);
        (new RawWriter($data))
            ->selectProperty($property)
            ->removeProperty()
            ->insertProperty($valueReader);
        $this->assertEquals($expectedData, $data);
    }


    public function providerExistingProperty(): array
    {
        return [
            'stringProperty' => [(object) ['a' => 'b'], 'a', 'b'],
            'numericPositiveProperty' => [(object) [1 => 'a'], '1', 'a'],
            'numericNegativeProperty' => [(object) [-1 => 'a'], '-1', 'a'],
            'numericZeroProperty' => [(object) [0 => 'a'], '0', 'a'],
        ];
    }


    /**
     * @param \stdClass $data
     * @param string $property
     * @dataProvider providerNonExistingProperty
     */
    public function testHasNoDataAfterNonExistingPropertySelection(\stdClass $data, string $property)
    {
        $hasData = (new RawWriter($data))
            ->selectProperty($property)
            ->hasData();
        $this->assertFalse($hasData);
    }


    /**
     * @param \stdClass $data
     * @param string $property
     * @dataProvider providerNonExistingProperty
     * @expectedException \Remorhaz\JSON\Data\Exception
     */
    public function testExceptionOnDataAccessAfterNonExistingPropertySelection(\stdClass $data, string $property)
    {
        (new RawWriter($data))
            ->selectProperty($property)
            ->getData();
    }


    /**
     * @param \stdClass $data
     * @param string $property
     * @dataProvider providerNonExistingProperty
     * @expectedException \LogicException
     */
    public function testSplExceptionOnDataAccessAfterNonExistingPropertySelection(\stdClass $data, string $property)
    {
        (new RawWriter($data))
            ->selectProperty($property)
            ->getData();
    }


    /**
     * @param mixed $data
     * @param string $property
     * @expectedException \Remorhaz\JSON\Data\Exception
     * @dataProvider providerNonObjectData
     */
    public function testExceptionOnNonObjectPropertySelection($data, string $property)
    {
        (new RawWriter($data))->selectProperty($property);
    }


    /**
     * @param mixed $data
     * @param string $property
     * @expectedException \LogicException
     * @dataProvider providerNonObjectData
     */
    public function testSplExceptionOnNonObjectPropertySelection($data, string $property)
    {
        (new RawWriter($data))->selectProperty($property);
    }


    /**
     * @param mixed $data
     * @dataProvider providerNonObjectData
     */
    public function testNoObjectSelectedAfterCreationWithNonObjectData($data)
    {
        $isObject = (new RawWriter($data))->isObjectSelected();
        $this->assertFalse($isObject);
    }


    /**
     * @param mixed $data
     * @dataProvider providerNonObjectData
     */
    public function testCorrectlyReportedNonSelectedPropertyInNonObjectData($data)
    {
        $isPropertySelected = (new RawWriter($data))->isPropertySelected();
        $this->assertFalse($isPropertySelected);
    }


    /**
     * @param mixed $data
     * @dataProvider providerNonObjectData
     * @expectedException \Remorhaz\JSON\Data\Exception
     */
    public function testExceptionOnRemovingNonSelectedPropertyInNonObjectData($data)
    {
        (new RawWriter($data))->removeProperty();
    }


    /**
     * @param mixed $data
     * @dataProvider providerNonObjectData
     * @expectedException \LogicException
     */
    public function testSplExceptionOnRemovingNonSelectedPropertyInNonObjectData($data)
    {
        (new RawWriter($data))->removeProperty();
    }


    public function providerNonObjectData(): array
    {
        return [
            'arrayData' => [[0 => 1], '0'],
            'booleanData' => [true, 'a'],
            'nullData' => [null, 'a'],
            'integerData' => [1, 'a'],
            'floatData' => [1.2, 'a'],
        ];
    }


    /**
     * @param \stdClass $data
     * @dataProvider providerObjectData
     */
    public function testCorrectlyReportedNonSelectedPropertyInObjectData(\stdClass $data)
    {
        $isPropertySelected = (new RawWriter($data))->isPropertySelected();
        $this->assertFalse($isPropertySelected);
    }


    /**
     * @param \stdClass $data
     * @dataProvider providerObjectData
     */
    public function testObjectIsSelectedAfterCreationWithObjectData(\stdClass $data)
    {
        $isObjectSelected = (new RawWriter($data))->isObjectSelected();
        $this->assertTrue($isObjectSelected);
    }


    /**
     * @param \stdClass $data
     * @dataProvider providerObjectData
     * @expectedException \Remorhaz\JSON\Data\Exception
     */
    public function testExceptionOnRemovingNonSelectedPropertyInObjectData(\stdClass $data)
    {
        (new RawWriter($data))->removeProperty();
    }


    /**
     * @param \stdClass $data
     * @dataProvider providerObjectData
     * @expectedException \LogicException
     */
    public function testSplExceptionOnRemovingNonSelectedPropertyInObjectData(\stdClass $data)
    {
        (new RawWriter($data))->removeProperty();
    }


    public function providerObjectData(): array
    {
        return [
            [(object) ['a' => 'b']],
        ];
    }


    /**
     * @param \stdClass $data
     * @param string $property
     * @param mixed $newValue
     * @param \stdClass $expectedData
     * @dataProvider providerReplacingExistingProperty
     */
    public function testCorrectDataAfterReplacingExistingProperty(
        \stdClass $data,
        string $property,
        $newValue,
        \stdClass $expectedData
    ) {
        $newValueReader = new RawWriter($newValue);
        (new RawWriter($data))
            ->selectProperty($property)
            ->replaceData($newValueReader);
        $this->assertEquals($expectedData, $data);
    }


    public function providerReplacingExistingProperty(): array
    {
        return [
            'stringProperty' => [(object) ['a' => 'b'], 'a', 'c', (object) ['a' => 'c']],
            'numericPositiveProperty' => [(object) [1 => 'a'], '1', 'b', (object) [1 => 'b']],
            'numericNegativeProperty' => [(object) [-1 => 'a'], '-1', 'b', (object) [-1 => 'b']],
            'numericZeroProperty' => [(object) [0 => 'a'], '0', 'b', (object) [0 => 'b']],
        ];
    }


    /**
     * @param \stdClass $data
     * @param string $property
     * @param mixed $newValue
     * @param mixed $oldValue
     * @dataProvider providerReplacingAndRestoringExistingProperty
     */
    public function testPropertyRemainsSelectedAfterReplacingExistingProperty(
        \stdClass $data,
        string $property,
        $newValue,
        $oldValue
    ) {
        $expectedData = clone $data;
        $newValueReader = new RawWriter($newValue);
        $oldValueReader = new RawWriter($oldValue);
        (new RawWriter($data))
            ->selectProperty($property)
            ->replaceData($newValueReader)
            ->replaceData($oldValueReader);
        $this->assertEquals($expectedData, $data);
    }


    public function providerReplacingAndRestoringExistingProperty(): array
    {
        return [
            'stringProperty' => [(object) ['a' => 'b'], 'a', 'c', 'b'],
            'numericPositiveProperty' => [(object) [1 => 'a'], '1', 'b', 'a'],
            'numericNegativeProperty' => [(object) [-1 => 'a'], '-1', 'b', 'a'],
            'numericZeroProperty' => [(object) [0 => 'a'], '0', 'b', 'a'],
        ];
    }


    /**
     * @param \stdClass $data
     * @param string $newProperty
     * @param mixed $newValue
     * @expectedException \Remorhaz\JSON\Data\Exception
     * @dataProvider providerNonExistingProperty
     */
    public function testExceptionOnReplacingNonExistingProperty(\stdClass $data, string $newProperty, $newValue)
    {
        $newValueReader = new RawWriter($newValue);
        (new RawWriter($data))
            ->selectProperty($newProperty)
            ->replaceData($newValueReader);
    }


    /**
     * @param \stdClass $data
     * @param string $newProperty
     * @param mixed $newValue
     * @expectedException \LogicException
     * @dataProvider providerNonExistingProperty
     */
    public function testSplExceptionOnReplacingNonExistingProperty(\stdClass $data, string $newProperty, $newValue)
    {
        $newValueReader = new RawWriter($newValue);
        (new RawWriter($data))
            ->selectProperty($newProperty)
            ->replaceData($newValueReader);
    }


    /**
     * @param \stdClass $data
     * @param string $newProperty
     * @param mixed $newValue
     * @param \stdClass $expectedData
     * @dataProvider providerNonExistingProperty
     */
    public function testCorrectDataAfterInsertingNonExistingProperty(
        \stdClass $data,
        string $newProperty,
        $newValue,
        \stdClass $expectedData
    ) {
        $newValueReader = new RawWriter($newValue);
        (new RawWriter($data))
            ->selectProperty($newProperty)
            ->insertProperty($newValueReader);
        $this->assertEquals($expectedData, $data);
    }


    /**
     * @param \stdClass $data
     * @param string $newProperty
     * @param mixed $newValue
     * @dataProvider providerNonExistingProperty
     */
    public function testPropertyRemainsSelectedAfterInsertingNonExistingProperty(
        \stdClass $data,
        string $newProperty,
        $newValue
    ) {
        $expectedData = clone $data;
        $newValueReader = new RawWriter($newValue);
        (new RawWriter($data))
            ->selectProperty($newProperty)
            ->insertProperty($newValueReader)
            ->removeProperty(); // If property is still selected, this returns data to initial state.
        $this->assertEquals($expectedData, $data);
    }


    /**
     * @param \stdClass $data
     * @param string $property
     * @dataProvider providerNonExistingProperty
     * @expectedException \Remorhaz\JSON\Data\Exception
     */
    public function testExceptionOnRemovingNonExistingProperty(\stdClass $data, string $property)
    {
        (new RawWriter($data))
            ->selectProperty($property)
            ->removeProperty();
    }


    /**
     * @param \stdClass $data
     * @param string $property
     * @dataProvider providerNonExistingProperty
     * @expectedException \LogicException
     */
    public function testSplExceptionOnRemovingNonExistingProperty(\stdClass $data, string $property)
    {
        (new RawWriter($data))
            ->selectProperty($property)
            ->removeProperty();
    }


    public function providerNonExistingProperty(): array
    {
        return [
            'stringProperty' => [(object) ['a' => 'b'], 'c', 'd', (object) ['a' => 'b', 'c' => 'd']],
            'numericPositiveProperty' => [(object) [0 => 'a'], '1', 'b', (object) [0 => 'a', 1 => 'b']],
            'numericNegativeProperty' => [(object) [0 => 'a'], '-1', 'b', (object) [0 => 'a', -1 => 'b']],
            'numericZeroProperty' => [(object) [1 => 'a'], '0', 'b', (object) [1 => 'a', 0 => 'b']],
        ];
    }


    /**
     * @param \stdClass $data
     * @param string $property
     * @param mixed $newValue
     * @expectedException \Remorhaz\JSON\Data\Exception
     * @dataProvider providerInsertingExistingProperty
     */
    public function testExceptionAfterInsertingExistingProperty(\stdClass $data, string $property, $newValue)
    {
        $newValueReader = new RawWriter($newValue);
        (new RawWriter($data))
            ->selectProperty($property)
            ->insertProperty($newValueReader);
    }


    /**
     * @param \stdClass $data
     * @param string $property
     * @param mixed $newValue
     * @expectedException \LogicException
     * @dataProvider providerInsertingExistingProperty
     */
    public function testSplExceptionAfterInsertingExistingProperty(\stdClass $data, string $property, $newValue)
    {
        $newValueReader = new RawWriter($newValue);
        (new RawWriter($data))
            ->selectProperty($property)
            ->insertProperty($newValueReader);
    }


    public function providerInsertingExistingProperty(): array
    {
        return [
            'stringProperty' => [(object) ['a' => 'b'], 'a', 'c'],
            'numericPositiveProperty' => [(object) [1 => 'a'], '1', 'b'],
            'numericNegativeProperty' => [(object) [-1 => 'a'], '-1', 'b'],
            'numericZeroProperty' => [(object) [0 => 'a'], '0', 'b'],
        ];
    }


    /**
     * @expectedException \Remorhaz\JSON\Data\Exception
     */
    public function testExceptionOnInsertingNotSelectedProperty()
    {
        $data = (object) ['a' => 'b'];
        $newValue = 'c';
        $newValueReader = new RawWriter($newValue);
        (new RawWriter($data))->insertProperty($newValueReader);
    }


    /**
     * @expectedException \LogicException
     */
    public function testSplExceptionOnInsertingNotSelectedProperty()
    {
        $data = (object) ['a' => 'b'];
        $newValue = 'c';
        $newValueReader = new RawWriter($newValue);
        (new RawWriter($data))->insertProperty($newValueReader);
    }


    /**
     * @param \stdClass $data
     * @param string $property
     * @dataProvider providerExistingProperty
     */
    public function testCorrectlyReportedSelectedProperty(\stdClass $data, string $property)
    {
        $isPropertySelected = (new RawWriter($data))
            ->selectProperty($property)
            ->isPropertySelected();
        $this->assertTrue($isPropertySelected);
    }


    /**
     * @param \stdClass $data
     * @param string $property
     * @param \stdClass $expectedData
     * @dataProvider providerRemovingExistingProperty
     */
    public function testCorrectDataAfterRemovingExistingProperty(
        \stdClass $data,
        string $property,
        \stdClass $expectedData
    ) {
        (new RawWriter($data))
            ->selectProperty($property)
            ->removeProperty();
        $this->assertEquals($expectedData, $data);
    }


    public function providerRemovingExistingProperty(): array
    {
        return [
            'stringProperty' => [(object) ['a' => 'b', 'c' => 'd'], 'a', (object) ['c' => 'd']],
            'numericPositiveProperty' => [(object) [0 => 'a', 1 => 'b'], '1', (object) [0 => 'a']],
            'numericNegativeProperty' => [(object) [0 => 'a', -1 => 'b'], '-1', (object) [0 => 'a']],
            'numericZeroProperty' => [(object) [0 => 'a', 1 => 'b'], '0', (object) [1 => 'b']],
        ];
    }
}
