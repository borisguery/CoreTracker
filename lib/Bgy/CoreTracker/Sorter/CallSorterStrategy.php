<?php

namespace Bgy\CoreTracker\Sorter;

use Bgy\CoreTracker\CoreDump;

class CallSorterStrategy implements SorterStrategyInterface
{
    private $reverse;

    public function __construct($reverse = false)
    {
        $this->reverse = (bool) $reverse;
    }

    /**
     * @param CoreDump $coredump
     * @param bool  $reverse
     * @return void
     */
    public function sort(CoreDump $coredump, $reverse = null)
    {
        $reverse = ($reverse) ? $reverse : $this->reverse;

        uasort($coredump->getCollectedData(), function($a, $b) use ($reverse) {

            if ($reverse) {
                return $b->calls > $a->calls;
            }

            return $b->calls < $a->calls;
        });
    }
}
