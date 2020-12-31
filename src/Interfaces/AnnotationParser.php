<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use InvalidArgumentException,
    NGSOFT\Exceptions\AnnotationParserException,
    ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

interface AnnotationParser {

    /**
     * Parse Annotation Block
     * @param string $docComment
     * @return AnnotationCollection
     */
    public function parseAnnotation(string $docComment): AnnotationCollection;

    /**
     * Parse Class Annotations using Class Name
     * @param string $className
     * @return AnnotationCollection
     * @throws AnnotationParserException On Error
     * @throws InvalidArgumentException on invalid Class Name
     */
    public function parseClassName(string $className): AnnotationCollection;

    /**
     * Parse Class Annotations
     * @param ReflectionClass $reflector
     * @return AnnotationCollection
     * @throws AnnotationParserException On Error
     */
    public function parseClass(ReflectionClass $reflector): AnnotationCollection;

    /**
     * Parse Class Method Annotations
     * @param ReflectionMethod $reflector
     * @return AnnotationCollection
     * @throws AnnotationParserException On Error
     */
    public function parseMethod(ReflectionMethod $reflector): AnnotationCollection;

    /**
     * Parse Class Property Annotations
     * @param ReflectionProperty $reflector
     * @return AnnotationCollection
     * @throws AnnotationParserException On Error
     */
    public function parseProperty(ReflectionProperty $reflector): AnnotationCollection;
}
