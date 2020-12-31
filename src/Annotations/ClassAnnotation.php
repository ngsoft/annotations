<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use NGSOFT\Annotations\Utils\AnnotationAbstract,
    ReflectionClass;

class ClassAnnotation extends AnnotationAbstract {

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

    /**
     * @param ReflectionClass $reflector
     * @param string $tag
     * @param mixed $value
     */
    public function __construct(ReflectionClass $reflector, string $tag, $value = null) {
        $this->reflector = $reflector;
        parent::__construct($tag, $value);
    }

}
