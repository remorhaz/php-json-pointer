<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

abstract class ReferenceEvaluate
{

    /**
     * @var Advancer|null
     */
    protected $advancer;

    protected $result;

    protected $isResultSet;


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


    abstract protected function performNonExisting();


    /**
     * @return Advancer
     */
    protected function getAdvancer()
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
    public function perform()
    {
        $canAdvance = $this
            ->getAdvancer()
            ->canAdvance();
        if ($canAdvance) {
            $this
                ->getAdvancer()
                ->advance();
            return $this;
        }
        return $this->performNonExisting();
    }

    public function isResultSet()
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
     * @return ReferenceEvaluate
     */
    protected function setResult(&$result)
    {
        $this->result = &$result;
        $this->isResultSet = true;
        return $this;
    }
}
