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

    /**
     * Adds a processor by class name
     * @param string $className
     * @return self
     * @throws InvalidArgumentException if $className does not implements AnnotationProcessor
     */
    public function add(string $className): self;
}
