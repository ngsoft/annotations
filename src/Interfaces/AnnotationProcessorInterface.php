<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface AnnotationProcessorInterface {

    /**
     * Process Annotation Value
     * @param AnnotationInterface $annotation
     * @param AnnotationHandlerInterface $handler
     * @return AnnotationInterface
     */
    public function process(AnnotationInterface $annotation, AnnotationHandlerInterface $handler): AnnotationInterface;
}
