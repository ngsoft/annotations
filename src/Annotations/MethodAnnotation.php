<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

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

    /**
     * @param ReflectionMethod $reflector
     * @param string $tag
     * @param mixed $value
     */
    public function __construct(ReflectionMethod $reflector, string $tag, $value) {
        $this->reflector = $reflector;
        parent::__construct($tag, $value);
    }

}
