<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use JsonSerializable,
    ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

interface AnnotationInterface extends JsonSerializable {

    const ANNOTATION_TYPE_CLASS = "CLASS";
    const ANNOTATION_TYPE_PROPERTY = "PROPERTY";
    const ANNOTATION_TYPE_METHOD = "METHOD";
    const ANNOTATION_TYPES = [
        self::ANNOTATION_TYPE_CLASS => ReflectionClass::class,
        self::ANNOTATION_TYPE_PROPERTY => ReflectionProperty::class,
        self::ANNOTATION_TYPE_METHOD => ReflectionMethod::class
    ];

    ///////////////////////////////// Shorthands  /////////////////////////////////

    /**
     * Get Annotation Tag Name
     * @return string
     */
    public function getTagName(): string;

    /**
     * Get Annotation Parsed Value
     * @return mixed
     */
    public function getTagValue();

    ///////////////////////////////// Configurators  /////////////////////////////////

    /**
     * Returns a new instance with the given tag
     * @param TagInterface $tag
     * @return AnnotationInterface
     */
    public function withTag(TagInterface $tag): AnnotationInterface;

    /**
     * Returns a new instance with the given tag value
     * @param mixed $value
     * @return AnnotationInterface
     */
    public function withTagValue($value): AnnotationInterface;

    ///////////////////////////////// GETTERS  /////////////////////////////////

    /**
     * Get the AnnotationTagInterface instance
     * @return TagInterface
     */
    public function getTag(): TagInterface;

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
     * Get the filename linked to the annotation
     * @return string
     */
    public function getFileName(): string;

    /**
     * Get The Reflector linked to the annotation
     * @return ReflectionClass|ReflectionProperty|ReflectionMethod
     */
    public function getReflector();
}
