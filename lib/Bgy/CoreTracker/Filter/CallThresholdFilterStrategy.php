<?php

namespace Bgy\CoreTracker\Filter;

use Bgy\CoreTracker\CollectedClass;

class CallThresholdFilterStrategy implements FilterStrategyInterface
{
    protected $threshold;

    public function __construct($threshold)
    {
        $this->threshold = $threshold;
    }

    /**
     * @param CollectedClass $collectedClass
     * @param bool $inverse
     * @return boolean
     */
    public function shouldBeFiltered(CollectedClass $collectedClass, $inverse = false)
    {

        return $inverse
            ? $collectedClass->calls > $this->threshold
            : $collectedClass->calls < $this->threshold
        ;
    }
}
