<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Processor;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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
use Remorhaz\JSON\Pointer\Processor\Mutator\DeleteElementMutation;
use Remorhaz\JSON\Pointer\Processor\Mutator\DeletePropertyMutation;
use Remorhaz\JSON\Pointer\Processor\Mutator\InsertElementMutation;
use Remorhaz\JSON\Pointer\Processor\Mutator\MutatorInterface;
use Remorhaz\JSON\Pointer\Processor\Mutator\ReplaceMutation;
use Remorhaz\JSON\Pointer\Processor\Processor;
use Remorhaz\JSON\Pointer\Query\QueryInterface;
use Remorhaz\JSON\Pointer\Query\QueryResult;
use Remorhaz\JSON\Pointer\Query\QueryResultInterface;

#[CoversClass(Processor::class)]
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
        $rootNode = self::createStub(NodeValueInterface::class);

        $query
            ->expects(self::once())
            ->method('__invoke')
            ->with(self::identicalTo($rootNode));
        $processor->select($query, $rootNode);
    }

    public function testSelect_QueryResultHasNoSelection_ResultNotExists(): void
    {
        $processor = Processor::create();
        $query = self::createStub(QueryInterface::class);

        $queryResult = self::createStub(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(false);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->select(
            $query,
            self::createStub(NodeValueInterface::class),
        );
        self::assertFalse($result->exists());
    }

    public function testSelect_QueryResultHasSelection_ResultExists(): void
    {
        $processor = Processor::create();
        $query = self::createStub(QueryInterface::class);

        $queryResult = self::createStub(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->select(
            $query,
            self::createStub(NodeValueInterface::class),
        );
        self::assertTrue($result->exists());
    }

    public function testSelect_QueryResultHasSelection_SelectionPassedToEncoderOnEncode(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $processor = new Processor(
            $encoder,
            self::createStub(ValueDecoderInterface::class),
            self::createStub(MutatorInterface::class)
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = self::createStub(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $selection = self::createStub(NodeValueInterface::class);
        $queryResult
            ->method('getSelection')
            ->willReturn($selection);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->select(
            $query,
            self::createStub(NodeValueInterface::class),
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
            self::createStub(ValueEncoderInterface::class),
            $decoder,
            self::createStub(MutatorInterface::class),
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = self::createStub(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $selection = self::createStub(NodeValueInterface::class);
        $queryResult
            ->method('getSelection')
            ->willReturn($selection);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->select(
            $query,
            $this->createMock(NodeValueInterface::class),
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
        $rootNode = self::createStub(NodeValueInterface::class);

        $query
            ->expects(self::once())
            ->method('__invoke')
            ->with(self::identicalTo($rootNode));
        $processor->delete($query, $rootNode);
    }

    public function testDelete_QueryResultHasNoSelectionButHasParent_ResultNotExists(): void
    {
        $processor = Processor::create();
        $query = self::createStub(QueryInterface::class);

        $queryResult = new QueryResult(
            '',
            null,
            self::createStub(NodeValueInterface::class)
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->delete(
            $query,
            self::createStub(NodeValueInterface::class)
        );
        self::assertFalse($result->exists());
    }

    public function testDelete_QueryResultHasSelectionButNoParent_ResultNotExists(): void
    {
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            self::createStub(MutatorInterface::class),
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->delete(
            $query,
            self::createStub(NodeValueInterface::class),
        );
        self::assertFalse($result->exists());
    }

    public function testDelete_ParentIsNotStruct_ResultNotExists(): void
    {
        $mutator = self::createStub(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $mutator
            ->method('mutate')
            ->willReturn($this->createMock(NodeValueInterface::class));
        $result = $processor->delete(
            $query,
            self::createStub(NodeValueInterface::class),
        );
        self::assertFalse($result->exists());
    }

    public function testDelete_MutatorReturnsNull_ResultNotExists(): void
    {
        $mutator = self::createStub(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
            new NodeArrayValue(
                [],
                self::createStub(PathInterface::class),
                self::createStub(NodeValueFactoryInterface::class),
            )
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $mutator
            ->method('mutate')
            ->willReturn(null);
        $result = $processor->delete(
            $query,
            self::createStub(NodeValueInterface::class),
        );
        self::assertFalse($result->exists());
    }

    public function testDelete_NonStructParent_ResultNotExists(): void
    {
        $mutator = self::createStub(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $mutator
            ->method('mutate')
            ->willReturn(self::createStub(NodeValueInterface::class));
        $result = $processor->delete(
            $query,
            self::createStub(NodeValueInterface::class),
        );
        self::assertFalse($result->exists());
    }

    public function testDelete_ParentIsArray_ResultExists(): void
    {
        $mutator = self::createStub(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
            new NodeArrayValue(
                [],
                self::createStub(PathInterface::class),
                self::createStub(NodeValueFactoryInterface::class),
            )
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);
        $mutator
            ->method('mutate')
            ->willReturn(self::createStub(NodeValueInterface::class));
        $result = $processor->delete(
            $query,
            self::createStub(NodeValueInterface::class),
        );
        self::assertTrue($result->exists());
    }

    public function testDelete_ParentIsObject_ResultExists(): void
    {
        $mutator = self::createStub(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
            new NodeObjectValue(
                (object) [],
                self::createStub(PathInterface::class),
                self::createStub(NodeValueFactoryInterface::class),
            )
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $mutator
            ->method('mutate')
            ->willReturn(self::createStub(NodeValueInterface::class));
        $result = $processor->delete(
            $query,
            self::createStub(NodeValueInterface::class),
        );
        self::assertTrue($result->exists());
    }

    public function testDelete_ParentIsArray_DeleteElementMutationPassedToMutator(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
            new NodeArrayValue(
                [],
                self::createStub(PathInterface::class),
                self::createStub(NodeValueFactoryInterface::class),
            )
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $rootNode = self::createStub(NodeValueInterface::class);
        $mutator
            ->expects(self::once())
            ->method('mutate')
            ->with(
                self::identicalTo($rootNode),
                self::isInstanceOf(DeleteElementMutation::class)
            );
        $processor->delete($query, $rootNode);
    }

    public function testDelete_ParentIsObject_DeletePropertyMutationPassedToMutator(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
            new NodeObjectValue(
                (object) [],
                self::createStub(PathInterface::class),
                self::createStub(NodeValueFactoryInterface::class),
            )
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $rootNode = self::createStub(NodeValueInterface::class);
        $mutator
            ->expects(self::once())
            ->method('mutate')
            ->with(
                self::identicalTo($rootNode),
                self::isInstanceOf(DeletePropertyMutation::class),
            );
        $processor->delete($query, $rootNode);
    }

    public function testDelete_MutatorReturnsValue_SameInstancePassedToEncoderOnEncode(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $mutator = self::createStub(MutatorInterface::class);
        $processor = new Processor(
            $encoder,
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
            new NodeArrayValue(
                [],
                self::createStub(PathInterface::class),
                self::createStub(NodeValueFactoryInterface::class),
            )
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);
        $mutatedNode = self::createStub(NodeValueInterface::class);
        $mutator
            ->method('mutate')
            ->willReturn($mutatedNode);
        $result = $processor->delete(
            $query,
            self::createStub(NodeValueInterface::class),
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
        $rootNode = self::createStub(NodeValueInterface::class);

        $query
            ->expects(self::once())
            ->method('__invoke')
            ->with(self::identicalTo($rootNode));
        $processor->replace(
            $query,
            $rootNode,
            self::createStub(NodeValueInterface::class),
        );
    }

    public function testReplace_QueryResultHasNoSelection_ResultNotExists(): void
    {
        $processor = Processor::create();
        $query = self::createStub(QueryInterface::class);

        $queryResult = self::createStub(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(false);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->replace(
            $query,
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        self::assertFalse($result->exists());
    }

    public function testReplace_QueryResultHasSelectionButMutatorReturnsNull_ResultNotExists(): void
    {
        $mutator = self::createStub(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = self::createStub(QueryResultInterface::class);
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
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        self::assertFalse($result->exists());
    }

    public function testReplace_MutatorReturnsValue_ResultExists(): void
    {
        $mutator = self::createStub(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = self::createStub(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $mutator
            ->method('mutate')
            ->willReturn(self::createStub(NodeValueInterface::class));
        $result = $processor->replace(
            $query,
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        self::assertTrue($result->exists());
    }

    public function testReplace_QueryResultHasSelection_ReplaceMutationPassedToMutator(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = self::createStub(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $rootNode = self::createStub(NodeValueInterface::class);
        $mutator
            ->expects(self::once())
            ->method('mutate')
            ->with(
                self::identicalTo($rootNode),
                self::isInstanceOf(ReplaceMutation::class),
            );
        $processor->replace(
            $query,
            $rootNode,
            self::createStub(NodeValueInterface::class),
        );
    }

    public function testReplace_MutatorReturnsValue_SameInstancePassedToEncoderOnEncode(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $mutator = self::createStub(MutatorInterface::class);
        $processor = new Processor(
            $encoder,
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $queryResult = self::createStub(QueryResultInterface::class);
        $queryResult
            ->method('hasSelection')
            ->willReturn(true);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);
        $mutatedNode = self::createStub(NodeValueInterface::class);
        $mutator
            ->method('mutate')
            ->willReturn($mutatedNode);
        $result = $processor->replace(
            $query,
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
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
        $rootNode = self::createStub(NodeValueInterface::class);

        $query
            ->expects(self::once())
            ->method('__invoke')
            ->with(self::identicalTo($rootNode));
        $processor->add(
            $query,
            $rootNode,
            self::createStub(NodeValueInterface::class),
        );
    }

    #[DataProvider('providerHasParentOrLastReference')]
    public function testAdd_QueryResultHasNoParentOrLastReference_ResultNotExists(
        bool $hasParent,
        bool $hasLastReference,
    ): void {
        $processor = Processor::create();
        $query = self::createStub(QueryInterface::class);

        $queryResult = self::createStub(QueryResultInterface::class);
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
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        self::assertFalse($result->exists());
    }

    /**
     * @return iterable<string, array{bool, bool}>
     */
    public static function providerHasParentOrLastReference(): iterable
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
        $query = self::createStub(QueryInterface::class);
        $queryResult = new QueryResult(
            '',
            null,
            self::createStub(NodeValueInterface::class),
            self::createStub(ReferenceInterface::class),
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->add(
            $query,
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        self::assertFalse($result->exists());
    }

    /**
     * @param class-string<ReferenceInterface> $lastReferenceClass
     */
    #[DataProvider('providerAddSelectableNonInsertable')]
    public function testAdd_QueryResultHasArrayParentHasSelectionHasNotIndexLastReference_ResultNotExists(
        string $lastReferenceClass,
    ): void {
        $processor = Processor::create();
        $query = self::createStub(QueryInterface::class);

        $parent = new NodeArrayValue(
            [],
            self::createStub(PathInterface::class),
            self::createStub(NodeValueFactoryInterface::class),
        );
        /** @var ReferenceInterface $reference */
        $reference = self::createStub($lastReferenceClass);
        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
            $parent,
            $reference,
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->add(
            $query,
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        self::assertFalse($result->exists());
    }

    /**
     * @return iterable<string, array{class-string<ReferenceInterface>}>
     */
    public static function providerAddSelectableNonInsertable(): iterable
    {
        return [
            'Next index' => [NextIndexReferenceInterface::class],
            'Non-index property' => [ReferenceInterface::class],
        ];
    }

    public function testAdd_QueryResultHasArrayParentHasNoSelectionHasPropertyLastReference_ResultNotExists(): void
    {
        $processor = Processor::create();
        $query = self::createStub(QueryInterface::class);

        $parent = new NodeArrayValue(
            [],
            self::createStub(PathInterface::class),
            self::createStub(NodeValueFactoryInterface::class),
        );
        $queryResult = new QueryResult(
            '',
            null,
            $parent,
            self::createStub(ReferenceInterface::class),
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $result = $processor->add(
            $query,
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        self::assertFalse($result->exists());
    }

    /**
     * @param string<ReferenceInterface> $lastReferenceClass
     */
    #[DataProvider('providerArrayParentIndexProperty')]
    public function testAdd_QueryResultHasArrayParentHasNoSelectionHasIndexProperty_ResultExists(
        string $lastReferenceClass,
    ): void {
        $mutator = self::createStub(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $parent = new NodeArrayValue(
            [],
            self::createStub(PathInterface::class),
            self::createStub(NodeValueFactoryInterface::class),
        );
        /** @var ReferenceInterface $reference */
        $reference = self::createStub($lastReferenceClass);
        $queryResult = new QueryResult('', null, $parent, $reference);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);
        $mutator
            ->method('mutate')
            ->willReturn(self::createStub(NodeValueInterface::class));
        $result = $processor->add(
            $query,
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        self::assertTrue($result->exists());
    }

    /**
     * @return iterable<string, array{class-string<ReferenceInterface>}>
     */
    public static function providerArrayParentIndexProperty(): iterable
    {
        return [
            'Next index' => [NextIndexReferenceInterface::class],
            'Index' => [IndexReferenceInterface::class],
        ];
    }

    /**
     * @param string $lastReferenceClass
     */
    #[DataProvider('providerArrayParentIndexProperty')]
    public function testAdd_QueryResultHasArrayParentHasNoSelectionHasIndexProperty_PassesAppendElementMutation(
        string $lastReferenceClass,
    ): void {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $parent = new NodeArrayValue(
            [],
            self::createStub(PathInterface::class),
            self::createStub(NodeValueFactoryInterface::class),
        );
        /** @var ReferenceInterface $reference */
        $reference = self::createStub($lastReferenceClass);
        $queryResult = new QueryResult('', null, $parent, $reference);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);
        $rootNode = self::createStub(NodeValueInterface::class);
        $mutator
            ->expects(self::once())
            ->method('mutate')
            ->with(self::identicalTo($rootNode), self::isInstanceOf(AppendElementMutation::class));
        $processor->add(
            $query,
            $rootNode,
            self::createStub(NodeValueInterface::class),
        );
    }

    public function testAdd_QueryResultHasArrayParentHasSelectionHasIndexProperty_ResultExists(): void
    {
        $mutator = self::createStub(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $parent = new NodeArrayValue(
            [],
            self::createStub(PathInterface::class),
            self::createStub(NodeValueFactoryInterface::class),
        );
        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
            $parent,
            self::createStub(IndexReferenceInterface::class),
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);
        $mutator
            ->method('mutate')
            ->willReturn(self::createStub(NodeValueInterface::class));
        $result = $processor->add(
            $query,
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        self::assertTrue($result->exists());
    }

    public function testAdd_QueryResultHasArrayParentHasSelectionHasIndexProperty_PassesInsertElementMutation(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );
        $query = self::createStub(QueryInterface::class);

        $parent = new NodeArrayValue(
            [],
            self::createStub(PathInterface::class),
            self::createStub(NodeValueFactoryInterface::class),
        );
        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
            $parent,
            self::createStub(IndexReferenceInterface::class),
        );
        $query
            ->method('__invoke')
            ->willReturn($queryResult);
        $rootNode = self::createStub(NodeValueInterface::class);
        $mutator
            ->expects(self::once())
            ->method('mutate')
            ->with(self::identicalTo($rootNode), self::isInstanceOf(InsertElementMutation::class));
        $processor->add(
            $query,
            $rootNode,
            self::createStub(NodeValueInterface::class),
        );
    }

    public function testAdd_QueryResultHasObjectParentHasSelection_PassesReplaceMutationToMutator(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );

        $parent = new NodeObjectValue(
            (object) [],
            self::createStub(PathInterface::class),
            self::createStub(NodeValueFactoryInterface::class),
        );
        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
            $parent,
            self::createStub(ReferenceInterface::class),
        );
        $query = self::createStub(QueryInterface::class);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $rootNode = self::createStub(NodeValueInterface::class);
        $mutator
            ->expects(self::once())
            ->method('mutate')
            ->with(self::identicalTo($rootNode), self::isInstanceOf(ReplaceMutation::class));
        $processor->add(
            $query,
            $rootNode,
            self::createStub(NodeValueInterface::class),
        );
    }

    public function testAdd_QueryResultHasObjectParentHasSelection_ResultExists(): void
    {
        $mutator = self::createStub(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );

        $parent = new NodeObjectValue(
            (object) [],
            self::createStub(PathInterface::class),
            self::createStub(NodeValueFactoryInterface::class),
        );
        $queryResult = new QueryResult(
            '',
            self::createStub(NodeValueInterface::class),
            $parent,
            self::createStub(ReferenceInterface::class),
        );
        $query = self::createStub(QueryInterface::class);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $mutator
            ->method('mutate')
            ->willReturn(self::createStub(NodeValueInterface::class));
        $result = $processor->add(
            $query,
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        self::assertTrue($result->exists());
    }

    public function testAdd_QueryResultHasObjectParentHasNoSelection_PassesAppendPropertyMutationToMutator(): void
    {
        $mutator = $this->createMock(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );

        $parent = new NodeObjectValue(
            (object) [],
            self::createStub(PathInterface::class),
            self::createStub(NodeValueFactoryInterface::class),
        );
        $queryResult = new QueryResult(
            '',
            null,
            $parent,
            self::createStub(ReferenceInterface::class),
        );
        $query = self::createStub(QueryInterface::class);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $rootNode = self::createStub(NodeValueInterface::class);
        $mutator
            ->expects(self::once())
            ->method('mutate')
            ->with(self::identicalTo($rootNode), self::isInstanceOf(AppendPropertyMutation::class));
        $processor->add(
            $query,
            $rootNode,
            self::createStub(NodeValueInterface::class),
        );
    }

    public function testAdd_QueryResultHasObjectParentHasNoSelection_ResultExists(): void
    {
        $mutator = self::createStub(MutatorInterface::class);
        $processor = new Processor(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $mutator,
        );

        $parent = new NodeObjectValue(
            (object) [],
            self::createStub(PathInterface::class),
            self::createStub(NodeValueFactoryInterface::class),
        );
        $queryResult = new QueryResult(
            '',
            null,
            $parent,
            self::createStub(ReferenceInterface::class),
        );
        $query = self::createStub(QueryInterface::class);
        $query
            ->method('__invoke')
            ->willReturn($queryResult);

        $mutator
            ->method('mutate')
            ->willReturn(self::createStub(NodeValueInterface::class));
        $result = $processor->add(
            $query,
            self::createStub(NodeValueInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        self::assertTrue($result->exists());
    }
}
