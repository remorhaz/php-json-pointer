<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

interface ReferenceInterface
{

    /**
     * Reference is an array index (decimal digit with no leading zeros).
     */
    const TYPE_INDEX = 0x01;

    /**
     * Reference is a non-existing array index (single "-" symbol).
     */
    const TYPE_NEXT_INDEX = 0x02;

    /**
     * Reference is an object property (any other string).
     */
    const TYPE_PROPERTY = 0x03;

    /**
     * Returns reference type.
     *
     * @return int
     * @throws LogicException
     */
    public function getType(): int;

    /**
     * Returns reference value.
     *
     * @return int|string
     * @throws LogicException
     */
    public function getKey();

    public function isLast(): bool;
}
