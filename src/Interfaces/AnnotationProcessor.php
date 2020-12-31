<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface AnnotationProcessor {

    /**
     * Process Annotation Value
     * @param Annotation $annotation
     * @param AnnotationHandler $handler
     * @param AnnotationFactoryInterface $factory
     * @return AnnotationCollection
     */
    public function process(Annotation $annotation, AnnotationHandler $handler, AnnotationFactoryInterface $factory): AnnotationCollection;
}
