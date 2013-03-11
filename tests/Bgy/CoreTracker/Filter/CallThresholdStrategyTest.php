<?php

namespace Bgy\CoreTracker\Filter;

use Bgy\CoreTracker\CollectedClass;

class CallThresholdFilterStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldBeFiltered()
    {
        $f = new CallThresholdFilterStrategy(10);
        $c = new CollectedClass('Foo');
        $c->calls = 5;
        $this->assertTrue($f->shouldBeFiltered($c));
        $this->assertFalse($f->shouldBeFiltered($c, true));
    }
}
