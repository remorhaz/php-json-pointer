<?php

namespace Remorhaz\JSON\Data;

interface WriterInterface extends ReaderInterface
{


    /**
     * @param ReaderInterface $source
     * @return $this
     */
    public function replaceData(ReaderInterface $source);


    /**
     * @param ReaderInterface $source
     * @return $this
     */
    public function insertProperty(ReaderInterface $source);


    /**
     * @return $this
     */
    public function removeProperty();


    /**
     * @param ReaderInterface $source
     * @return $this
     */
    public function appendElement(ReaderInterface $source);


    /**
     * @return $this
     */
    public function removeElement();
}
