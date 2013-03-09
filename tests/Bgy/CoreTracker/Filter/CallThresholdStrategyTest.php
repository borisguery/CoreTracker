<?php

namespace Bgy\CoreTracker\Filter;

class CallThresholdFilterStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldBeFiltered()
    {
        $f = new CallThresholdFilterStrategy(10);
         $f->shouldBeFiltered(array('calls' => 5));
    }
}
