<?php

namespace Remorhaz\JSONPointer\Evaluator;

class AdvancerScalar extends Advancer
{


    public function canAdvance()
    {
        return false;
    }


    protected function &advance(&$cursorData)
    {
        throw new LogicException("Scalar is not advanceable");
    }


    public function canWrite()
    {
        return false;
    }


    public function fail()
    {
        throw new EvaluatorException("Using key {$this->getKeyDescription()} on scalar data");
    }


    public function write($data)
    {
        throw new LogicException("Scalar is not writable");
    }


    public function delete()
    {
        throw new LogicException("Scalar can't contain deletable data");
    }
}
