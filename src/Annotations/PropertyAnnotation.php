<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

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

    /**
     * @param ReflectionProperty $reflector
     * @param string $tag
     * @param mixed $value
     */
    public function __construct(ReflectionProperty $reflector, string $tag, $value = null) {
        $this->reflector = $reflector;
        parent::__construct($tag, $value);
    }

}
