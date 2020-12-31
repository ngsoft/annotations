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
     * @return Annotation
     * @throws InvalidArgumentException if invalid processor
     */
    public function createAnnotation($reflector, string $tag): Annotation;

    /**
     * Creates a new Annotation Collection
     * @param Annotation $annotations
     * @return AnnotationCollection
     */
    public function createAnnotationCollection(Annotation ...$annotations): AnnotationCollection;
}
