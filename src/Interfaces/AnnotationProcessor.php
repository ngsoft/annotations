<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface AnnotationProcessor {

    /**
     * Process Annotation Value
     * @param AnnotationInterface $annotation
     * @param AnnotationHandler $handler
     * @param AnnotationFactoryInterface $factory
     * @return AnnotationCollectionInterface
     */
    public function process(AnnotationInterface $annotation, AnnotationHandler $handler, AnnotationFactoryInterface $factory): AnnotationCollectionInterface;
}
