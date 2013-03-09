<?php

namespace Bgy\CoreTracker\Filter;

class NamespaceFilterStrategy implements FilterStrategyInterface
{
    protected $namespaces;

    protected $inverse;

    public function __construct(array $namespaces, $inverse = false)
    {
        $this->namespaces = $namespaces;
        $this->inverse = $inverse;
    }

    /**
     * @param array $collectedClass
     * @param bool $inverse
     * @return boolean
     */
    public function shouldBeFiltered($collectedClass, $inverse = null)
    {
        $inverse = $inverse ?: $this->inverse;

        $shouldBeFiltered = false;
        foreach ($this->namespaces as $namespace) {
            $shouldBeFiltered = $inverse
                ? false === strpos($collectedClass['className'], $namespace)
                : 0 === strpos($collectedClass['className'], $namespace);
        }

//        var_dump($shouldBeFiltered);

        return $shouldBeFiltered;
    }
}
