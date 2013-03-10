<?php


namespace Bgy\CoreTracker\Filter;


use Bgy\CoreTracker\CollectedClass;

interface FilterStrategyInterface {

    public function shouldBeFiltered(CollectedClass $collectedClass, $inverse = false);
}
