<?php


namespace Bgy\CoreTracker;

class CollectedClass {

    public $className;

    public $calls = 0;

    public function __construct($className)
    {
        $this->className = $className;
    }
}
