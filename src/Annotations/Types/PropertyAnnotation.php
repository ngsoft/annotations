<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Types;

use ReflectionProperty;

class PropertyAnnotation extends AnnotationBasic {

    /** @var ReflectionProperty */
    protected $reflector;

    /** @return ReflectionProperty */
    public function getReflector() {
        return $this->reflector;
    }

}
