<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluator;

use Remorhaz\JSONPointer\Locator;
use Remorhaz\JSONPointer\Locator\Reference;

/**
 * Abstract JSON Pointer evaluator.
 *
 * @package JSONPointer
 */
abstract class LocatorEvaluator
{

    /**
     * Locator object.
     *
     * @var Locator|null
     */
    private $locator;

    /**
     * Data for evaluation.
     *
     * @var mixed
     */
    private $data;

    /**
     * Data setup flag.
     *
     * @var bool
     */
    private $isDataSet = false;

    /**
     * Link to data for the reference being evaluated..
     *
     * @var Cursor|null
     */
    private $cursor;

    /**
     * Result setup flag.
     *
     * @var bool
     */
    protected $isResultSet = false;

    /**
     * Evaluation result.
     *
     * @var mixed
     */
    protected $result;

    /**
     * @var bool
     */
    protected $nonNumericIndices = false;


    /**
     * Constructor.
     */
    protected function __construct()
    {
    }


    /**
     * Creates object instance.
     *
     * @return static
     */
    public static function factory()
    {
        return new static();
    }


    /**
     * Sets locator object.
     *
     * @param Locator $locator
     * @return $this
     */
    public function setLocator(Locator $locator)
    {
        $this->locator = $locator;
        return $this;
    }


    /**
     * Returns locator object.
     *
     * @return Locator
     * @throws LogicException
     */
    protected function getLocator()
    {
        if (null === $this->locator) {
            throw new LogicException("Locator is not set in evaluator");
        }
        return $this->locator;
    }


    /**
     * Sets data for evaluation.
     *
     * @param mixed $data
     * @return $this
     */
    public function setData(&$data)
    {
        $this->data = &$data;
        $this->isDataSet = true;
        return $this;
    }


    /**
     * Returns data for evaluation.
     *
     * @return mixed
     * @throws LogicException
     */
    protected function &getData()
    {
        if (!$this->isDataSet) {
            throw new LogicException("Data is not set in evaluator");
        }
        return $this->data;
    }


    /**
     * Sets evaluation result.
     *
     * @param mixed $result
     * @return $this
     */
    protected function setResult(&$result)
    {
        $this->result = &$result;
        $this->isResultSet = true;
        return $this;
    }


    /**
     * Resets evaluation result.
     *
     * @return $this
     */
    protected function resetResult()
    {
        unset($this->result);
        $this->isResultSet = false;
        return $this;
    }


    /**
     * Returns evaluation result.
     *
     * @return mixed
     * @throws LogicException
     */
    public function &getResult()
    {
        if (!$this->isResultSet) {
            throw new LogicException("Evaluation result is not set");
        }
        return $this->result;
    }


    protected function setCursor(Cursor $cursor)
    {
        $this->cursor = $cursor;
        return $this;
    }


    protected function getCursor()
    {
        if (null === $this->cursor) {
            throw new LogicException("Cursor is not set in reference evaluator");
        }
        return $this->cursor;
    }


    protected function resetCursor()
    {
        $cursor = Cursor::factory()
            ->setData($this->getData());
        return $this->setCursor($cursor);
    }


    public function allowNonNumericIndices()
    {
        $this->nonNumericIndices = true;
        return $this;
    }


    public function forbidNonNumericIndices()
    {
        $this->nonNumericIndices = false;
        return $this;
    }


    /**
     * Performs the evaluation.
     *
     * @return $this
     * @throws LogicException
     */
    public function evaluate()
    {
        $this
            ->resetCursor()
            ->resetResult()
            ->evaluateReferenceList();
        if (!$this->isResultSet) {
            $this->evaluateLocator();
        }
        if (!$this->isResultSet) {
            throw new LogicException("Data evaluation failed");
        }
        return $this;
    }


    protected function createAdvancer()
    {
        $advancer = Advancer::byCursorFactory($this->getCursor());
        if ($advancer instanceof AdvancerNonNumericIndex && $this->nonNumericIndices) {
            $advancer->allow();
        }
        return $advancer;
    }


    /**
     * @return ReferenceEvaluator
     */
    abstract protected function createReferenceEvaluator();


    protected function evaluateReferenceList()
    {
        $referenceList = $this
            ->getLocator()
            ->getReferenceList();
        foreach ($referenceList as $reference) {
            $this
                ->getCursor()
                ->setReference($reference);
            $referenceEvaluator = $this->createReferenceEvaluator();
            try {
                $referenceEvaluator->evaluate();
            } catch (EvaluatorException $e) {
                throw new EvaluatorException(
                    "Error evaluating data for path '{$reference->getPath()}': {$e->getMessage()}",
                    null,
                    $e
                );
            }
            if ($referenceEvaluator->isResultSet()) {
                $this->setResult($referenceEvaluator->getResult());
                return $this;
            }
        }
        return $this;
    }


    abstract protected function evaluateLocator();
}
