<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use NGSOFT\Exceptions\AnnotationException;

interface AnnotationProcessorInterface {

    /**
     * Process Annotation Value
     * @param AnnotationInterface $annotation
     * @param AnnotationHandlerInterface $handler
     * @return AnnotationInterface
     * @throws AnnotationException on Error
     */
    public function process(AnnotationInterface $annotation, AnnotationHandlerInterface $handler): AnnotationInterface;
}
