<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Types;

use NGSOFT\Annotations\Utils\AnnotationAbstract,
    ReflectionProperty;

class PropertyAnnotation extends AnnotationAbstract {

    /** @var ReflectionProperty */
    protected $reflector;

    /** @return ReflectionProperty */
    public function getReflector() {
        return $this->reflector;
    }

    /** {@inheritdoc} */
    public function getType(): string {
        return self::ANNOTATION_TYPE_PROPERTY;
    }

    /** {@inheritdoc} */
    protected function setReflector($reflector) {
        $this->reflector = $reflector;
    }

}
