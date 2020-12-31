<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

interface AnnotationInterface {

    const ANNOTATION_TYPE_CLASS = "CLASS";
    const ANNOTATION_TYPE_PROPERTY = "PROPERTY";
    const ANNOTATION_TYPE_METHOD = "METHOD";

    /**
     * Get Annotation Tag
     * @return string
     */
    public function getTag(): string;

    /**
     * Get Annotation Parsed Value
     * @return mixed
     */
    public function getValue();

    /**
     * Get Annotation Type (as defined ANNOTATION_TYPE_*)
     * @return string
     */
    public function getType(): string;

    /**
     * Get Annotation Class Name
     * @return string
     */
    public function getClassName(): string;

    /**
     * Get Class/Property/Method name
     * @return string
     */
    public function getName(): string;

    /**
     * Get The Reflector linked to the annotation
     * @return ReflectionClass|ReflectionProperty|ReflectionMethod
     */
    public function getReflector();

    /**
     * Returns a new instance with specified value
     * @param mixed $value
     * @return AnnotationInterface
     */
    public function withValue($value): AnnotationInterface;
}
