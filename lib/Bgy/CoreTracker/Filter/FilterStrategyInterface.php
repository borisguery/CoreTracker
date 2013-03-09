<?php


namespace Bgy\CoreTracker\Filter;


interface FilterStrategyInterface {

    public function shouldBeFiltered($collectedClass, $inverse = false);
}
