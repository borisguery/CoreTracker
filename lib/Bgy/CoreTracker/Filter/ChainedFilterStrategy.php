<?php

namespace Bgy\CoreTracker\Filter;

use Bgy\CoreTracker\CollectedClass;

class ChainedFilterStrategy implements FilterStrategyInterface
{
    private $filters = array();

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param CollectedClass $collectedClass
     * @param bool $inverse
     * @return boolean
     */
    public function shouldBeFiltered(CollectedClass $collectedClass, $inverse = false)
    {
        $shouldBeFiltered = false;
        /** @var $filter FilterStrategyInterface */
        foreach ($this->filters as $filter) {
            $shouldBeFiltered |= $filter->shouldBeFiltered($collectedClass, $inverse);
        }

        return $shouldBeFiltered;
    }
}
