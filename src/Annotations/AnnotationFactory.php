<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use InvalidArgumentException;
use NGSOFT\Interfaces\{
    AnnotationFactoryInterface, AnnotationInterface
};
use ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

class AnnotationFactory implements AnnotationFactoryInterface {

    /** {@inheritdoc} */
    public function createAnnotation($reflector, string $tag, $value = null): AnnotationInterface {
        if ($reflector instanceof ReflectionClass) return new ClassAnnotation($reflector, $tag, $value);
        elseif ($reflector instanceof ReflectionProperty) return new PropertyAnnotation($reflector, $tag, $value);
        elseif ($reflector instanceof ReflectionMethod) return new MethodAnnotation($reflector, $tag, $value);
        throw new InvalidArgumentException(sprintf('Invalid Reflector Class %s', get_class($reflector)));
    }

}
