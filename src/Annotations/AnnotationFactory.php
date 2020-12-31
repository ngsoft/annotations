<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use InvalidArgumentException;
use NGSOFT\Interfaces\{
    AnnotationCollectionInterface, AnnotationFactoryInterface, AnnotationInterface
};
use ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

class AnnotationFactory implements AnnotationFactoryInterface {

    /** {@inheritdoc} */
    public function createAnnotation($reflector, string $tag): AnnotationInterface {

        if ($reflector instanceof ReflectionClass) return new ClassAnnotation($reflector, $tag);
        elseif ($reflector instanceof ReflectionProperty) return new PropertyAnnotation($reflector, $tag);
        elseif ($reflector instanceof ReflectionMethod) return new MethodAnnotation($reflector, $tag);
        throw new InvalidArgumentException(sprintf('Invalid Reflector Class %s', get_class($reflector)));
    }

    /** {@inheritdoc} */
    public function createAnnotationCollection(AnnotationInterface ...$annotations): AnnotationCollectionInterface {
        return (new AnnotationCollection)->addAnnotation(...$annotations);
    }

}
