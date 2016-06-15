<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

abstract class ReferenceAdvanceable extends ReferenceEvaluate
{

    /**
     * @var Advancer|null
     */
    protected $advanceCursor;


    /**
     * @return Advancer
     */
    abstract protected function createAdvancer();

    abstract protected function performNonExisting();

    /**
     * @return Advancer
     */
    protected function getAdvancer()
    {
        if (null === $this->advanceCursor) {
            $this->advanceCursor = $this
                ->createAdvancer()
                ->setReference($this->getReference())
                ->setDataCursor($this->getDataCursor());
        }
        return $this->advanceCursor;
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
            $dataCursor = &$this
                ->getAdvancer()
                ->advance()
                ->getNewDataCursor();
            return $this->setDataCursor($dataCursor);
        }
        return $this->performNonExisting();
    }
}
