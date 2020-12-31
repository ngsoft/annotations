<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use InvalidArgumentException;

interface AnnotationProcessorDispatcher {

    /**
     * Add a processor to the stack
     * Works as a middleware
     * @param AnnotationProcessor $processor
     * @return self
     */
    public function addProcessor(AnnotationProcessor $processor): self;
}
