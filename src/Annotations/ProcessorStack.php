<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use ArrayIterator,
    IteratorAggregate;
use NGSOFT\Interfaces\{
    AnnotationProcessor, AnnotationProcessorStack
};

class ProcessorStack implements AnnotationProcessorStack, IteratorAggregate {

    /** @var AnnotationProcessor[] */
    private $processors = [];

    /** {@inheritdoc} */
    public function addProcessors(AnnotationProcessor ...$processors): AnnotationProcessorStack {
        foreach ($processors as $processor) {
            if (!in_array($processor, $this->processors)) {
                $this->processors[] = $processor;
            }
        }
        return $this;
    }

    /** {@inheritdoc} */
    public function getStack(): array {

        return $this->processors;
    }

    /** {@inheritdoc} */
    public function getIterator() {
        return new ArrayIterator($this->getStack());
    }

}
