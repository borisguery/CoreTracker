<?php

namespace Bgy\CoreTracker\Sorter;

use Bgy\CoreTracker\CoreDump;

class ChainedSorterStrategy implements SorterStrategyInterface
{
    private $sorters = array();

    public function __construct(array $sorters)
    {
        $this->sorters = $sorters;
    }

    /**
     * @param CoreDump $coredump
     * @param bool  $reverse
     * @return void
     */
    public function sort(CoreDump $coredump, $reverse = false)
    {
        /** @var $sorter SorterStrategyInterface */
        foreach ($this->sorters as $sorter) {
            $sorter->sort($coredump);
        }
    }
}
