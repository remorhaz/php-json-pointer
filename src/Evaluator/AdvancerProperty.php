<?php

namespace Remorhaz\JSONPointer\Evaluator;

class AdvancerProperty extends Advancer
{


    public function canAdvance()
    {
        $key = $this->getKey();
        $data = $this
            ->getCursor()
            ->getData();
        if ($this->isNumericKey($key)) {
            // Numeric properties can be accessed only through iteration in PHP.
            foreach ($data as $property => $value) {
                if ($property === (int) $key) {
                    return true;
                }
            }
            return false;
        }
        return property_exists($data, $key);
    }


    protected function &advance(&$cursorData)
    {
        $key = $this->getKey();
        if ($this->isNumericKey($key)) {
            // Numeric properties can be accessed only through iteration in PHP.
            foreach ($cursorData as $property => &$value) {
                if ($property === (int) $key) {
                    return $value;
                }
            }
            unset($value);
            throw new EvaluatorException("Failed to advance to numeric property {$key} in object");
        }
        return $cursorData->{$key};
    }


    /**
     * Numeric properties cannot be accessed directrly in PHP, so we should detect them.
     *
     * @param string $key
     * @return bool
     */
    protected function isNumericKey($key)
    {
        return preg_match('/^-?\d+$/u', $key) === 1;
    }


    public function write($data)
    {
        $key = $this->getKey();
        $cursorData = &$this
            ->getCursor()
            ->getData();
        $cursorData->{$key} = $data;
        return $this;
    }


    public function delete()
    {
        $cursorData = $this
            ->getCursor()
            ->getData();
        unset($cursorData->{$this->getKey()});
        return $this;
    }


    public function fail()
    {
        throw new EvaluatorException("Property {$this->getKeyDescription()} is not found");
    }
}
