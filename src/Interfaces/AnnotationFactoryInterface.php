<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use InvalidArgumentException,
    ReflectionClass,
    ReflectionClassConstant,
    ReflectionMethod,
    ReflectionProperty;

interface AnnotationFactoryInterface {

    /**
     * Creates a new Annotation
     * @param ReflectionClass|ReflectionProperty|ReflectionMethod|ReflectionClassConstant $reflector
     * @param TagInterface $tag
     * @return AnnotationInterface
     * @throws InvalidArgumentException if invalid reflector
     */
    public function createAnnotation($reflector, TagInterface $tag): AnnotationInterface;

    /**
     * Creates a new Tag
     * @param string $name
     * @param mixed $value
     * @return TagInterface
     * @throws InvalidArgumentException if invalid tag name
     */
    public function createTag(string $name, $value = null): TagInterface;
}
