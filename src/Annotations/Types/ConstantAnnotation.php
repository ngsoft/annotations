<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Types;

use ReflectionClassConstant;

class ConstantAnnotation extends AnnotationBasic {

    /** @var ReflectionClassConstant */
    protected $reflector;

    /** @return ReflectionClassConstant */
    public function getReflector() {
        return $this->reflector;
    }

}
