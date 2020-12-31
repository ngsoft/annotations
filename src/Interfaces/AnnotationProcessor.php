<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface AnnotationProcessor {

    /**
     * Filter Annotation Value
     * @param Annotation $annotation
     * @param AnnotationHandler $handler
     * @return Annotation
     */
    public function process(Annotation $annotation, AnnotationHandler $handler): Annotation;
}
