<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use InvalidArgumentException,
    IteratorAggregate;
use NGSOFT\Interfaces\{
    AnnotationFilterInterface, AnnotationInterface
};

/**
 * Filter Stack
 */
class AnnotationFilter implements AnnotationFilterInterface, IteratorAggregate {

    /** @var AnnotationFilterInterface[] */
    protected $filters = [];

    public function __construct(array $filters = []) {

        foreach ($filters as $filter) {
            if (!($filter instanceof AnnotationFilterInterface)) {
                throw new InvalidArgumentException('Invalid filter, not an instance of ' . AnnotationFilterInterface::class);
            }
            $this->filters[] = $filter;
        }
    }

    /** {@inheritdoc} */
    public function filter(AnnotationInterface $annotation): bool {
        foreach ($this->filters as $filter) {
            if (!$filter->filter($annotation)) return false;
        }
        // also if no filters
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
     * @return \Generator<string, AnnotationFilterInterface>
     */
    public function getIterator() {
        foreach ($this->filters as $filter) {
            yield get_class($filter) => $filter;
        }
    }

}
