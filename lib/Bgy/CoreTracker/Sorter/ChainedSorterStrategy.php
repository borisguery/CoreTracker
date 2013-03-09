<?php

namespace Bgy\CoreTracker\Sorter;

class ChainedSorterStrategy implements SorterStrategyInterface
{
    private $sorter = array();

    public function __construct(array $sorter)
    {
        $this->sorter = $sorter;
    }

    /**
     * @param array $collectedData
     * @param bool  $reverse
     * @return void
     */
    public function sort(array &$collectedData, $reverse = false)
    {
        /** @var $sorter SorterStrategyInterface */
        foreach ($this->sorter as $sorter) {
            $sorter->sort($collectedData);
        }
    }
}
