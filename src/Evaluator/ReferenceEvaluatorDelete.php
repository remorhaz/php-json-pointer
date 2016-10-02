<?php

namespace Remorhaz\JSONPointer\Evaluator;

class ReferenceEvaluatorDelete extends ReferenceEvaluator
{


    protected function doAdvanceCursor()
    {
        $reference = $this
            ->getAdvancer()
            ->getCursor()
            ->getReference();
        if ($reference->isLast()) {
            // Delete data by key instead of advancing cursor.
            $result = $this
                ->getAdvancer()
                ->getCursor()
                ->getData();
            $this
                ->setResult($result)
                ->getAdvancer()
                ->delete();
            return $this;
        }
        return parent::doAdvanceCursor();
    }


    protected function doAdvanceCursorFail()
    {
        $this
            ->getAdvancer()
            ->fail();
        return $this;
    }
}
