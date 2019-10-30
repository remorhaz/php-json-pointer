<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

interface QueryCapabilitiesInterface
{

    public function pointsNewElement(): bool;
}
