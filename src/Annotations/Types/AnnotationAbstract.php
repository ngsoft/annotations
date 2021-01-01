<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Types;

use InvalidArgumentException,
    NGSOFT\Interfaces\AnnotationInterface,
    ReflectionClass,
    ReflectionClassConstant,
    ReflectionMethod,
    ReflectionProperty,
    Reflector,
    RuntimeException;

abstract class AnnotationAbstract implements AnnotationInterface {

    /** @var AnnotationTagInterface */
    protected $tag;

    /** @var mixed */
    protected $value;

    /** @var string */
    protected $className;

    /** @var string */
    protected $fileName;

    /** @var string */
    protected $name;

    /** @var ReflectionClass|ReflectionMethod|ReflectionProperty|ReflectionClassConstant */
    protected $reflector;

    /**
     * @param ReflectionClass|ReflectionProperty|ReflectionMethod $reflector
     * @param string $tag
     * @param mixed $value
     */
    public function __construct($reflector, string $tag, $value) {
        if (
                !($reflector instanceof ReflectionClass)
                and!($reflector instanceof ReflectionMethod)
                and!($reflector instanceof ReflectionProperty)
        ) throw new InvalidArgumentException('Invalid Reflector Provided.');

        $this->tag = $tag;
        $this->value = $value;
        $this->setReflector($reflector);
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

    /** {@inheritdoc} */
    public function jsonSerialize() {

        return [
            'className' => $this->getClassName(),
            'type' => $this->getType(),
            'name' => $this->getName(),
            'reflector' => get_class($this->getReflector()),
            'filename' => $this->getFileName(),
            'tag' => $this->getTag(),
            'value' => $this->getValue()
        ];
    }

    /** {@inheritdoc} */
    public function serialize() {
        $array = $this->jsonSerialize();
        return \serialize($array);
    }

    /** {@inheritdoc} */
    public function unserialize($serialized) {

        $array = \unserialize($serialized);
        if (!is_array($array)) throw new RuntimeException('Invalid data provided');

        $this->tag = $array['tag'];
        $this->value = $array['value'];

        $reflectorClass = $array['reflector'];
        $class = $array['className'];
        $name = $array['name'];
        switch ($reflectorClass) {

            case ReflectionClass::class:
                $reflector = new \ReflectionClass($class);
                break;
            case ReflectionClassConstant::class:
            case ReflectionProperty::class:
            case ReflectionMethod::class:
                $reflector = new $reflectorClass($class, $name);
                break;
        }
        if (isset($reflector)) $this->setReflector($reflector);
    }

}
