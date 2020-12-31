<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use NGSOFT\Annotations\Interfaces\Annotation,
    ReflectionClass,
    ReflectionMethod,
    ReflectionProperty,
    RuntimeException;

abstract class AnnotationAbstract implements Annotation {

    /** @var string */
    protected $tag;

    /** @var mixed */
    protected $value;

    /**
     * @param string $tag
     * @param mixed $value
     */
    public function __construct(string $tag, $value) {
        $this->tag = $tag;
        $this->value = $value;
    }

    /** {@inheritdoc} */
    public function getClassName(): string {
        $reflector = $this->getReflector();
        if ($reflector instanceof ReflectionClass) return $reflector->getName();
        elseif ($reflector instanceof ReflectionProperty or $reflector instanceof ReflectionMethod) return $reflector->class;
        else throw new RuntimeException('Invalid Reflector Provided.');
    }

    /** {@inheritdoc} */
    public function getName(): string {
        return $this->getReflector()->getName();
    }

    /** {@inheritdoc} */
    public function getTag(): string {
        return $this->tag;
    }

    /** {@inheritdoc} */
    public function getValue() {
        return $this->value;
    }

}
