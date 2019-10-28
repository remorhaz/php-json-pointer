<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

final class QueryCapabilities implements QueryCapabilitiesInterface
{

    private $pointsNewElement;

    public function __construct(bool $pointsNewElement)
    {
        $this->pointsNewElement = $pointsNewElement;
    }

    public function pointsNewElement(): bool
    {
        return $this->pointsNewElement;
    }
}
