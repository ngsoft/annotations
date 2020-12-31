<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use Traversable;

interface AnnotationProcessorStack extends Traversable {

    /**
     * Adds Multiple processors to the stack
     * @param AnnotationProcessor ...$processors
     * @return self
     */
    public function addProcessors(AnnotationProcessor ...$processors): self;

    /**
     * Get Processors List
     * @return AnnotationProcessor[]
     */
    public function getStack(): array;
}
