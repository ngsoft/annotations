<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use NGSOFT\{
    Annotations\Filters\NullFilter, Interfaces\AnnotationFilterInterface, Interfaces\AnnotationInterface
};

/**
 * Filter Stack
 */
class AnnotationFilter implements AnnotationFilterInterface, \IteratorAggregate {

    /** @var AnnotationFilterInterface[] */
    protected $filters = [];

    public function __construct() {
        $this->filters[] = new NullFilter();
    }

    /** {@inheritdoc} */
    public function filter(AnnotationInterface $annotation): bool {
        foreach ($this->filters as $filter) {
            if (!$filter->filter($annotation)) return false;
        }
        return true;
    }

    /**
     * Adds a filter to the stack
     *
     * @param AnnotationFilterInterface $filter
     * @return static
     */
    public function addFilter(AnnotationFilterInterface $filter): self {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * @return Generator<string, AnnotationFilterInterface>
     */
    public function getIterator() {
        foreach ($this->filters as $filter) {
            yield get_class($filter) => $filter;
        }
    }

}
