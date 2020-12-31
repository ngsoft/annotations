<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use InvalidArgumentException,
    ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

interface AnnotationFactoryInterface {

    /**
     * Creates a new Annotation
     * @param ReflectionClass|ReflectionProperty|ReflectionMethod $reflector
     * @param string $tag
     * @return AnnotationInterface
     * @throws InvalidArgumentException if invalid processor
     */
    public function createAnnotation($reflector, string $tag): AnnotationInterface;

    /**
     * Creates a new Annotation Collection
     * @param AnnotationInterface ...$annotations
     * @return AnnotationCollectionInterface
     */
    public function createAnnotationCollection(AnnotationInterface ...$annotations): AnnotationCollectionInterface;
}
