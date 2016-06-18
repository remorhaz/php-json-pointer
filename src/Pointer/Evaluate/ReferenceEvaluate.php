<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

abstract class ReferenceEvaluate
{

    protected $result;

    protected $isResultSet;


    /**
     * Performs reference evaluation.
     *
     * @return $this
     */
    abstract public function perform();


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


    public function isResultSet()
    {
        return $this->isResultSet;
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
}
