<?php

namespace Remorhaz\JSONPointer\Evaluator;

abstract class ReferenceEvaluator
{

    /**
     * @var Advancer|null
     */
    protected $advancer;

    protected $result;

    protected $isResultSet = false;


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


    abstract protected function doAdvanceCursorFail();


    /**
     * @return Advancer
     */
    protected function getAdvancer(): Advancer
    {
        if (null === $this->advancer) {
            throw new LogicException("Advancer is not set in reference evaluator");
        }
        return $this->advancer;
    }


    public function setAdvancer(Advancer $advancer)
    {
        $this->advancer = $advancer;
        return $this;
    }


    /**
     * Performs reference evaluation.
     *
     * @return $this
     */
    public function evaluate()
    {
        $canAdvance = $this
            ->getAdvancer()
            ->canAdvance();
        return $canAdvance
            ? $this->doAdvanceCursor()
            : $this->doAdvanceCursorFail();
    }


    protected function doAdvanceCursor()
    {
        $this
            ->getAdvancer()
            ->advanceCursor();
        return $this;
    }

    public function isResultSet(): bool
    {
        return $this->isResultSet;
    }


    /**
     * Returns evaluation result.
     *
     * @return mixed
     * @throws LogicException
     */
    public function &getResult()
    {
        if (!$this->isResultSet()) {
            throw new LogicException("Evaluation result is not set");
        }
        return $this->result;
    }


    /**
     * Sets evaluation result.
     *
     * @param mixed $result
     * @return ReferenceEvaluator
     */
    protected function setResult(&$result)
    {
        $this->result = &$result;
        $this->isResultSet = true;
        return $this;
    }
}
