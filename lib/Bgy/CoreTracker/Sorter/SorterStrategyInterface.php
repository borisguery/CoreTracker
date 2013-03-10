<?php

namespace Bgy\CoreTracker\Sorter;

use Bgy\CoreTracker\CoreDump;

interface SorterStrategyInterface
{
    public function sort(CoreDump $coredump, $reverse = false);
}
