<?php

namespace Bgy\CoreTracker;

use Bgy\CoreTracker\Sorter\SorterStrategyInterface;

class CoreDump implements \ArrayAccess, \Serializable, \Iterator {

    protected $collectedClasses = array();

    public function offsetExists($offset)
    {
        return isset($this->collectedClasses[$offset]);
    }

    public function offsetGet($offset)
    {
        if (!isset($this->collectedClasses[$offset])) {

            $this->collectedClasses[$offset] = new CollectedClass($offset);
        }

        return $this->collectedClasses[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof CollectedClass) {
            throw new \InvalidArgumentException('$value must be a instance of CollectedClass');
        }

        $this->collectedClasses[$value->className] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->collectedClasses[$offset]);
    }

    public function serialize()
    {
        return serialize($this->collectedClasses);
    }

    public function unserialize($serialized)
    {
        $this->collectedClasses = unserialize($serialized);
    }

    public function current()
    {
        return current($this->collectedClasses);
    }

    public function next()
    {
        next($this->collectedClasses);
    }

    public function key()
    {
        return key($this->collectedClasses);
    }

    public function valid()
    {
        return null !== key($this->collectedClasses);
    }

    public function rewind()
    {
        reset($this->collectedClasses);
    }

    public function &getCollectedData()
    {
        return $this->collectedClasses;
    }
}
