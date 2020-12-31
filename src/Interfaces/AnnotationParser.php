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
     * @return array<string,mixed> key/values pair
     */
    public function parseAnnotation(string $docComment): array;

    /**
     * Parse Class Annotations using Class Name
     * @param string $className
     * @return Annotation[]
     * @throws AnnotationParserException On Error
     * @throws InvalidArgumentException on invalid Class Name
     */
    public function parseClassName(string $className): array;

    /**
     * Parse Class Annotations
     * @param ReflectionClass $reflector
     * @return array
     * @throws AnnotationParserException On Error
     */
    public function parseClass(ReflectionClass $reflector): array;

    /**
     * Parse Class Method Annotations
     * @param ReflectionMethod $reflector
     * @return array
     * @throws AnnotationParserException On Error
     */
    public function parseMethod(ReflectionMethod $reflector): array;

    /**
     * Parse Class Property Annotations
     * @param ReflectionProperty $reflector
     * @return array
     * @throws AnnotationParserException On Error
     */
    public function parseProperty(ReflectionProperty $reflector): array;
}
