<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Types;

use NGSOFT\Annotations\Utils\AnnotationAbstract,
    ReflectionMethod;

class MethodAnnotation extends AnnotationAbstract {

    /** @var ReflectionMethod */
    protected $reflector;

    /** @return ReflectionMethod */
    public function getReflector() {
        return $this->reflector;
    }

    /** {@inheritdoc} */
    public function getType(): string {
        return self::ANNOTATION_TYPE_METHOD;
    }

    /** {@inheritdoc} */
    protected function setReflector($reflector) {
        $this->reflector = $reflector;
    }

}
