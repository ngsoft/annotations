<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Types;

use ReflectionMethod;

class MethodAnnotation extends AnnotationBasic {

    /** @var ReflectionMethod */
    protected $reflector;

    /** @return ReflectionMethod */
    public function getReflector() {
        return $this->reflector;
    }

}
