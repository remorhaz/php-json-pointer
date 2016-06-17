<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

abstract class ReferenceAdvanceable extends ReferenceEvaluate
{

    /**
     * @var Advancer|null
     */
    protected $advancer;


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
            ->setReference($this->getReference())
            ->setDataCursor($this->getDataCursor())
            ->canAdvance();
        if ($canAdvance) {
            $dataCursor = &$this
                ->getAdvancer()
                ->advance()
                ->getNewDataCursor();
            return $this->setDataCursor($dataCursor);
        }
        return $this->performNonExisting();
    }
}
