<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Types;

use ReflectionClass;

class ClassAnnotation extends AnnotationBasic {

    /** @var ReflectionClass */
    protected $reflector;

    /** @return ReflectionClass */
    public function getReflector() {
        return $this->reflector;
    }

    /** {@inheritdoc} */
    public function getType(): string {
        return self::ANNOTATION_TYPE_CLASS;
    }

}
