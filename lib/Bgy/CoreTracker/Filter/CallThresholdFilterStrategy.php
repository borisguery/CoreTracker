<?php

namespace Bgy\CoreTracker\Filter;

class CallThresholdFilterStrategy implements FilterStrategyInterface
{
    protected $threshold;

    public function __construct($threshold)
    {
        $this->threshold = $threshold;
    }

    /**
     * @param array $collectedClass
     * @param bool $inverse
     * @return boolean
     */
    public function shouldBeFiltered($collectedClass, $inverse = false)
    {

        return $inverse
            ? $collectedClass['calls'] > $this->threshold
            : $collectedClass['calls'] < $this->threshold
        ;
    }
}
