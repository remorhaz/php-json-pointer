<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Processor;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeArrayValue;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeObjectValue;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactoryInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Locator\IndexReferenceInterface;
use Remorhaz\JSON\Pointer\Locator\NextIndexReferenceInterface;
use Remorhaz\JSON\Pointer\Locator\ReferenceInterface;
use Remorhaz\JSON\Pointer\Processor\Mutator\AppendElementMutation;
use Remorhaz\JSON\Pointer\Processor\Mutator\AppendPropertyMutation;
use Remorhaz\JSON\Pointer\Processor\Mutator\DeleteMutation;
use Remorhaz\JSON\Pointer\Processor\Mutator\InsertElementMutation;
use Remorhaz\JSON\Pointer\Processor\Mutator\MutatorInterface;
use Remorhaz\JSON\Pointer\Processor\Mutator\ReplaceMutation;
use Remorhaz\JSON\Pointer\Processor\Processor;
use Remorhaz\JSON\Pointer\Query\QueryInterface;
use Remorhaz\JSON\Pointer\Query\QueryResult;
use Remorhaz\JSON\Pointer\Query\QueryResultInterface;

/**
 * @covers \Remorhaz\JSON\Pointer\Processor\Processor
 */
class ProcessorTest extends TestCase
{

    public function testCreate_Always_ReturnsProcessorInstance(): void
    {
        self::assertInstanceOf(Processor::class, Processor::create());
    }

    public function testSelect_GivenQuery_ExecutesSameInstance(): void
    {
        $processor = Processor::create();
        $query = $this->createMock(QueryInterface::class);
        $rootNode = $this->createMock(NodeValueInterface::class);

        $query
            ->expects(self::once())
            ->method('__invoke')
            ->with(self::identicalTo($rootNode));
        $processor->select($query, $rootNode);
    }

    public function testSelect_QueryResultHasNoSelection_ResultNotExists(): void
    {
        $processor = Processor::create();
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(false);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->select(
            $query,
            $this->createMock(NodeValueInterface::class)
        );
        self::assertFalse($result->exists());
    }

    public function testSelect_QueryResultHasSelection_ResultExists(): void
    {
        $processor = Processor::create();
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->select(
            $query,
            $this->createMock(NodeValueInterface::class)
        );
        self::assertTrue($result->exists());
    }

    public function testSelect_QueryResultHasSelection_SelectionPassedToEncoderOnEncode(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $processor = new Processor(
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(MutatorInterface::class)
        );
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $selection = $this->createMock(NodeValueInterface::class);
        $queryResult
            ->method('getSelection')
            ->willReturn($selection);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->select(
            $query,
            $this->createMock(NodeValueInterface::class)
        );

        $encoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($selection));
        $result->encode();
    }

    public function testSelect_QueryResultHasSelection_SelectionPassedToDecoderOnDecode(): void
    {
        $decoder = $this->createMock(ValueDecoderInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $decoder,
            $this->createMock(MutatorInterface::class)
        );
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $selection = $this->createMock(NodeValueInterface::class);
        $queryResult
            ->method('getSelection')
            ->willReturn($selection);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->select(
            $query,
            $this->createMock(NodeValueInterface::class)
        );

        $decoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($selection));
        $result->decode();
    }

    public function testDelete_GivenQuery_ExecutesSameInstance(): void
    {
        $processor = Processor::create();
        $query = $this->createMock(QueryInterface::class);
        $rootNode = $this->createMock(NodeValueInterface::class);

        $query
            ->expects(self::once())
            ->method('__invoke')
            ->with(self::identicalTo($rootNode));
        $processor->delete($query, $rootNode);
    }

    public function testDelete_QueryResultHasNoSelection_ResultNotExists(): void
    {
        $processor = Processor::create();
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(false);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->delete(
            $query,
            $this->createMock(NodeValueInterface::class)
        );
        self::assertFalse($result->exists());
    }

    public function testDelete_QueryResultHasSelectionButMutatorReturnsNull_ResultNotExists(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator,
        );
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $mutator
            ->method('mutate')
            ->willReturn(null);
        $result = $processor->delete(
            $query,
            $this->createMock(NodeValueInterface::class)
        );
        self::assertFalse($result->exists());
    }

    public function testDelete_MutatorReturnsValue_ResultExists(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator,
        );
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $mutator
            ->method('mutate')
            ->willReturn($this->createMock(NodeValueInterface::class));
        $result = $processor->delete(
            $query,
            $this->createMock(NodeValueInterface::class)
        );
        self::assertTrue($result->exists());
    }

    public function testDelete_QueryResultHasSelection_DeleteMutationPassedToMutator(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator,
        );
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $rootNode = $this->createMock(NodeValueInterface::class);
        $mutator
            ->expects(self::once())
            ->method('mutate')
            ->with(
                self::identicalTo($rootNode),
                self::isInstanceOf(DeleteMutation::class)
            );
        $processor->delete($query, $rootNode);
    }

    public function testDelete_MutatorReturnsValue_SameInstancePassedToEncoderOnEncode(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
            $mutator,
        );
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);
        $mutatedNode = $this->createMock(NodeValueInterface::class);
        $mutator
            ->method('mutate')
            ->willReturn($mutatedNode);
        $result = $processor->delete(
            $query,
            $this->createMock(NodeValueInterface::class)
        );
        $encoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($mutatedNode));
        $result->encode();
    }

    public function testReplace_GivenQuery_ExecutesSameInstance(): void
    {
        $processor = Processor::create();
        $query = $this->createMock(QueryInterface::class);
        $rootNode = $this->createMock(NodeValueInterface::class);

        $query
            ->expects(self::once())
            ->method('__invoke')
            ->with(self::identicalTo($rootNode));
        $processor->replace(
            $query,
            $rootNode,
            $this->createMock(NodeValueInterface::class)
        );
    }

    public function testReplace_QueryResultHasNoSelection_ResultNotExists(): void
    {
        $processor = Processor::create();
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(false);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->replace(
            $query,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertFalse($result->exists());
    }

    public function testReplace_QueryResultHasSelectionButMutatorReturnsNull_ResultNotExists(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator,
        );
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $mutator
            ->method('mutate')
            ->willReturn(null);
        $result = $processor->replace(
            $query,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertFalse($result->exists());
    }

    public function testReplace_MutatorReturnsValue_ResultExists(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator,
        );
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $mutator
            ->method('mutate')
            ->willReturn($this->createMock(NodeValueInterface::class));
        $result = $processor->replace(
            $query,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertTrue($result->exists());
    }

    public function testReplace_QueryResultHasSelection_ReplaceMutationPassedToMutator(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator,
        );
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $rootNode = $this->createMock(NodeValueInterface::class);
        $mutator
            ->expects(self::once())
            ->method('mutate')
            ->with(
                self::identicalTo($rootNode),
                self::isInstanceOf(ReplaceMutation::class)
            );
        $processor->replace(
            $query,
            $rootNode,
            $this->createMock(NodeValueInterface::class)
        );
    }

    public function testReplace_MutatorReturnsValue_SameInstancePassedToEncoderOnEncode(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
            $mutator,
        );
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);
        $mutatedNode = $this->createMock(NodeValueInterface::class);
        $mutator
            ->method('mutate')
            ->willReturn($mutatedNode);
        $result = $processor->replace(
            $query,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        $encoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($mutatedNode));
        $result->encode();
    }

    public function testAdd_GivenQuery_ExecutesSameInstance(): void
    {
        $processor = Processor::create();
        $query = $this->createMock(QueryInterface::class);
        $rootNode = $this->createMock(NodeValueInterface::class);

        $query
            ->expects(self::once())
            ->method('__invoke')
            ->with(self::identicalTo($rootNode));
        $processor->add(
            $query,
            $rootNode,
            $this->createMock(NodeValueInterface::class)
        );
    }

    /**
     * @param bool $hasParent
     * @param bool $hasLastReference
     * @dataProvider providerHasParentOrLastReference
     */
    public function testAdd_QueryResultHasNoParentOrLastReference_ResultNotExists(
        bool $hasParent,
        bool $hasLastReference
    ): void {
        $processor = Processor::create();
        $query = $this->createMock(QueryInterface::class);

        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult
            ->method('hasParent')
            ->willReturn($hasParent);
        $queryResult
            ->method('hasLastReference')
            ->willReturn($hasLastReference);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->replace(
            $query,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertFalse($result->exists());
    }

    public function providerHasParentOrLastReference(): array
    {
        return [
            'No parent nor last reference' => [false, false],
            'Only parent' => [true, false],
            'Only last reference' => [false, true],
        ];
    }

    public function testAdd_QueryResultHasNonStructParent_ResultNotExists(): void
    {
        $processor = Processor::create();
        $query = $this->createMock(QueryInterface::class);
        $queryResult = new QueryResult(
            '',
            null,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(ReferenceInterface::class)
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->add(
            $query,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertFalse($result->exists());
    }

    /**
     * @param string $lastReferenceClass
     * @dataProvider providerAddSelectableNonInsertable
     */
    public function testAdd_QueryResultHasArrayParentHasSelectionHasNotIndexLastReference_ResultNotExists(
        string $lastReferenceClass
    ): void {
        $processor = Processor::create();
        $query = $this->createMock(QueryInterface::class);

        $parent = new NodeArrayValue(
            [],
            $this->createMock(PathInterface::class),
            $this->createMock(NodeValueFactoryInterface::class)
        );
        /** @var ReferenceInterface $reference */
        $reference = $this->createMock($lastReferenceClass);
        $queryResult = new QueryResult(
            '',
            $this->createMock(NodeValueInterface::class),
            $parent,
            $reference
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->add(
            $query,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertFalse($result->exists());
    }

    public function providerAddSelectableNonInsertable(): array
    {
        return [
            'Next index' => [NextIndexReferenceInterface::class],
            'Non-index property' => [ReferenceInterface::class],
        ];
    }

    public function testAdd_QueryResultHasArrayParentHasNoSelectionHasPropertyLastReference_ResultNotExists(): void
    {
        $processor = Processor::create();
        $query = $this->createMock(QueryInterface::class);

        $parent = new NodeArrayValue(
            [],
            $this->createMock(PathInterface::class),
            $this->createMock(NodeValueFactoryInterface::class)
        );
        $queryResult = new QueryResult(
            '',
            null,
            $parent,
            $this->createMock(ReferenceInterface::class)
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->add(
            $query,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertFalse($result->exists());
    }

    /**
     * @param string $lastReferenceClass
     * @dataProvider providerArrayParentIndexProperty
     */
    public function testAdd_QueryResultHasArrayParentHasNoSelectionHasIndexProperty_ResultExists(
        string $lastReferenceClass
    ): void {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator
        );
        $query = $this->createMock(QueryInterface::class);

        $parent = new NodeArrayValue(
            [],
            $this->createMock(PathInterface::class),
            $this->createMock(NodeValueFactoryInterface::class)
        );
        /** @var ReferenceInterface $reference */
        $reference = $this->createMock($lastReferenceClass);
        $queryResult = new QueryResult('', null, $parent, $reference);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);
        $mutator
            ->method('mutate')
            ->willReturn($this->createMock(NodeValueInterface::class));
        $result = $processor->add(
            $query,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertTrue($result->exists());
    }

    public function providerArrayParentIndexProperty(): array
    {
        return [
            'Next index' => [NextIndexReferenceInterface::class],
            'Index' => [IndexReferenceInterface::class],
        ];
    }

    /**
     * @param string $lastReferenceClass
     * @dataProvider providerArrayParentIndexProperty
     */
    public function testAdd_QueryResultHasArrayParentHasNoSelectionHasIndexProperty_PassesAppendElementMutation(
        string $lastReferenceClass
    ): void {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator
        );
        $query = $this->createMock(QueryInterface::class);

        $parent = new NodeArrayValue(
            [],
            $this->createMock(PathInterface::class),
            $this->createMock(NodeValueFactoryInterface::class)
        );
        /** @var ReferenceInterface $reference */
        $reference = $this->createMock($lastReferenceClass);
        $queryResult = new QueryResult('', null, $parent, $reference);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);
        $rootNode = $this->createMock(NodeValueInterface::class);
        $mutator
            ->expects(self::once())
            ->method('mutate')
            ->with(self::identicalTo($rootNode), self::isInstanceOf(AppendElementMutation::class));
        $processor->add(
            $query,
            $rootNode,
            $this->createMock(NodeValueInterface::class)
        );
    }

    public function testAdd_QueryResultHasArrayParentHasSelectionHasIndexProperty_ResultExists(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator
        );
        $query = $this->createMock(QueryInterface::class);

        $parent = new NodeArrayValue(
            [],
            $this->createMock(PathInterface::class),
            $this->createMock(NodeValueFactoryInterface::class)
        );
        $queryResult = new QueryResult(
            '',
            $this->createMock(NodeValueInterface::class),
            $parent,
            $this->createMock(IndexReferenceInterface::class)
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);
        $mutator
            ->method('mutate')
            ->willReturn($this->createMock(NodeValueInterface::class));
        $result = $processor->add(
            $query,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertTrue($result->exists());
    }

    public function testAdd_QueryResultHasArrayParentHasSelectionHasIndexProperty_PassesInsertElementMutation(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator
        );
        $query = $this->createMock(QueryInterface::class);

        $parent = new NodeArrayValue(
            [],
            $this->createMock(PathInterface::class),
            $this->createMock(NodeValueFactoryInterface::class)
        );
        $queryResult = new QueryResult(
            '',
            $this->createMock(NodeValueInterface::class),
            $parent,
            $this->createMock(IndexReferenceInterface::class)
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);
        $rootNode = $this->createMock(NodeValueInterface::class);
        $mutator
            ->expects(self::once())
            ->method('mutate')
            ->with(self::identicalTo($rootNode), self::isInstanceOf(InsertElementMutation::class));
        $processor->add(
            $query,
            $rootNode,
            $this->createMock(NodeValueInterface::class)
        );
    }

    public function testAdd_QueryResultHasObjectParentHasSelection_PassesReplaceMutationToMutator(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator
        );

        $parent = new NodeObjectValue(
            (object) [],
            $this->createMock(PathInterface::class),
            $this->createMock(NodeValueFactoryInterface::class)
        );
        $queryResult = new QueryResult(
            '',
            $this->createMock(NodeValueInterface::class),
            $parent,
            $this->createMock(ReferenceInterface::class)
        );
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $rootNode = $this->createMock(NodeValueInterface::class);
        $mutator
            ->expects(self::once())
            ->method('mutate')
            ->with(self::identicalTo($rootNode), self::isInstanceOf(ReplaceMutation::class));
        $processor->add(
            $query,
            $rootNode,
            $this->createMock(NodeValueInterface::class)
        );
    }

    public function testAdd_QueryResultHasObjectParentHasSelection_ResultExists(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator
        );

        $parent = new NodeObjectValue(
            (object) [],
            $this->createMock(PathInterface::class),
            $this->createMock(NodeValueFactoryInterface::class)
        );
        $queryResult = new QueryResult(
            '',
            $this->createMock(NodeValueInterface::class),
            $parent,
            $this->createMock(ReferenceInterface::class)
        );
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $mutator
            ->method('mutate')
            ->willReturn($this->createMock(NodeValueInterface::class));
        $result = $processor->add(
            $query,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertTrue($result->exists());
    }

    public function testAdd_QueryResultHasObjectParentHasNoSelection_PassesAppendPropertyMutationToMutator(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator
        );

        $parent = new NodeObjectValue(
            (object) [],
            $this->createMock(PathInterface::class),
            $this->createMock(NodeValueFactoryInterface::class)
        );
        $queryResult = new QueryResult(
            '',
            null,
            $parent,
            $this->createMock(ReferenceInterface::class)
        );
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $rootNode = $this->createMock(NodeValueInterface::class);
        $mutator
            ->expects(self::once())
            ->method('mutate')
            ->with(self::identicalTo($rootNode), self::isInstanceOf(AppendPropertyMutation::class));
        $processor->add(
            $query,
            $rootNode,
            $this->createMock(NodeValueInterface::class)
        );
    }

    public function testAdd_QueryResultHasObjectParentHasNoSelection_ResultExists(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $mutator
        );

        $parent = new NodeObjectValue(
            (object) [],
            $this->createMock(PathInterface::class),
            $this->createMock(NodeValueFactoryInterface::class)
        );
        $queryResult = new QueryResult(
            '',
            null,
            $parent,
            $this->createMock(ReferenceInterface::class)
        );
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $mutator
            ->method('mutate')
            ->willReturn($this->createMock(NodeValueInterface::class));
        $result = $processor->add(
            $query,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertTrue($result->exists());
    }
}
