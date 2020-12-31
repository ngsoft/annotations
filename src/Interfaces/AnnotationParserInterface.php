<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use InvalidArgumentException,
    NGSOFT\Exceptions\AnnotationParserException,
    ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

interface AnnotationParserInterface {

    /**
     * Parse Annotation Block without filtering
     * @param string $docComment
     * @return array<string,string>
     */
    public function parseAnnotation(string $docComment): array;

    /**
     * Parse Class Annotations using Class Name
     * @param string $className
     * @return AnnotationCollectionInterface
     * @throws AnnotationParserException On Error
     * @throws InvalidArgumentException on invalid Class Name
     */
    public function parseClassName(string $className): AnnotationCollectionInterface;

    /**
     * Parse Class Annotations
     * @param ReflectionClass $reflector
     * @return AnnotationCollectionInterface
     * @throws AnnotationParserException On Error
     */
    public function parseClass(ReflectionClass $reflector): AnnotationCollectionInterface;

    /**
     * Parse Class Method Annotations
     * @param ReflectionMethod $reflector
     * @return AnnotationCollectionInterface
     * @throws AnnotationParserException On Error
     */
    public function parseMethod(ReflectionMethod $reflector): AnnotationCollectionInterface;

    /**
     * Parse Class Property Annotations
     * @param ReflectionProperty $reflector
     * @return AnnotationCollectionInterface
     * @throws AnnotationParserException On Error
     */
    public function parseProperty(ReflectionProperty $reflector): AnnotationCollectionInterface;
}
