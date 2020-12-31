<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Interfaces;

use InvalidArgumentException,
    NGSOFT\Annotations\Exceptions\ParserException,
    ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

interface Parser {

    /**
     * Parse Class Annotations using Class Name
     * @param string $className
     * @return Annotation[]
     * @throws ParserException On Error
     * @throws InvalidArgumentException on invalid Class Name
     */
    public function parseClassName(string $className): array;

    /**
     * Parse Class Annotations
     * @param ReflectionClass $reflector
     * @return array
     * @throws ParserException On Error
     */
    public function parseClass(ReflectionClass $reflector): array;

    /**
     * Parse Class Method Annotations
     * @param ReflectionMethod $reflector
     * @return array
     * @throws ParserException On Error
     */
    public function parseMethod(ReflectionMethod $reflector): array;

    /**
     * Parse Class Property Annotations
     * @param ReflectionProperty $reflector
     * @return array
     * @throws ParserException On Error
     */
    public function parseProperty(ReflectionProperty $reflector): array;
}
