<?php

namespace Bgy\CoreTracker\Sorter;

interface SorterStrategyInterface
{
    public function sort(array &$collectedData, $reverse = false);
}
