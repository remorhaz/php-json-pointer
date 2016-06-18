<?php

namespace Remorhaz\JSONPointer\Evaluator;

use Remorhaz\JSONPointer\Locator\Reference;

class Cursor
{

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var bool
     */
    protected $isDataSet = false;

    /**
     * @var Reference|null
     */
    protected $reference;


    protected function __construct()
    {
    }


    /**
     * @return static
     */
    public static function factory()
    {
        return new static();
    }


    /**
     * @param Reference $reference
     * @return $this
     */
    public function setReference(Reference $reference)
    {
        $this->reference = $reference;
        return $this;
    }


    /**
     * @return Reference
     */
    public function getReference()
    {
        if (null === $this->reference) {
            throw new LogicException("Reference is not set in cursor");
        }
        return $this->reference;
    }


    /**
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
     * @return mixed
     */
    public function &getData()
    {
        if (!$this->isDataSet) {
            throw new LogicException("Data is not set in cursor");
        }
        return $this->data;
    }
}
