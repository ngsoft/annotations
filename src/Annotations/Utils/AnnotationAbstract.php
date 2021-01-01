<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use NGSOFT\Interfaces\AnnotationInterface,
    ReflectionClass,
    ReflectionMethod,
    ReflectionProperty,
    RuntimeException;

abstract class AnnotationAbstract implements AnnotationInterface {

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
    public function getFileName(): string {

        $reflector = $this->getReflector();
        if ($reflector instanceof ReflectionProperty or $reflector instanceof ReflectionMethod) {
            $reflector = new ReflectionClass($this->getClassName());
        }
        if ($reflector instanceof ReflectionClass) return $reflector->getFileName();
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

    /** {@inheritdoc} */
    public function withValue($value): AnnotationInterface {
        $clone = clone $this;
        $clone->value = $value;
        return $clone;
    }

}
