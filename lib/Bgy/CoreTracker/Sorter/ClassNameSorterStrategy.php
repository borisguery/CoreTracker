<?php

namespace Bgy\CoreTracker\Sorter;

class ClassNameSorterStrategy implements SorterStrategyInterface
{
    private $reverse;

    public function __construct($reverse = false)
    {
        $this->reverse = (bool) $reverse;
    }

    /**
     * @param array $collectedData
     * @param bool  $reverse
     * @return void
     */
    public function sort(array &$collectedData, $reverse = null)
    {
        $reverse = ($reverse) ? $reverse : $this->reverse;

        usort($collectedData, function($a, $b) use ($reverse) {

            if ($reverse) {
                return strcasecmp($b['className'], $a['className']);
            }

            return strcasecmp($a['className'], $b['className']);
        });
    }
}
