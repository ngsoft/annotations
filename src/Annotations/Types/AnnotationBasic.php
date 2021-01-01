<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Types;

use InvalidArgumentException;
use NGSOFT\Interfaces\{
    AnnotationInterface, TagInterface
};
use ReflectionClass,
    ReflectionMethod,
    ReflectionProperty,
    RuntimeException;

class AnnotationBasic implements AnnotationInterface {

    /** @var TagInterface */
    protected $tag;

    /** @var string */
    protected $className;

    /** @var string */
    protected $fileName;

    /** @var string */
    protected $name;

    /** @var string */
    protected $type;

    /** @var ReflectionClass|ReflectionMethod|ReflectionProperty */
    protected $reflector;

    /**
     * Assert Valid Reflector
     * @param mixed $reflector
     * @throws InvalidArgumentException
     */
    protected function assertValidReflection($reflector) {

        if (
                $reflector instanceof ReflectionClass
                or $reflector instanceof ReflectionProperty
                or $reflector instanceof ReflectionMethod
        ) return;
        throw new InvalidArgumentException('Invalid Reflector Provided.');
    }

    /**
     * @param ReflectionClass|ReflectionProperty|ReflectionMethod $reflector
     * @param TagInterface $tag
     */
    public function __construct($reflector, TagInterface $tag) {
        $this->assertValidReflection($reflector);
        $this->reflector = $reflector;
        $this->tag = $tag;
        $this->type = array_search(get_class($reflector), self::ANNOTATION_TYPES);
        $this->name = $reflector->getName();
        if ($reflector instanceof ReflectionClass) {
            $this->className = $reflector->getName();
            $this->fileName = $reflector->getFileName();
        } elseif (
                $reflector instanceof ReflectionProperty
                or $reflector instanceof ReflectionMethod
        ) {
            $this->className = $reflector->class;
            $this->fileName = (new ReflectionClass($reflector->class))->getFileName();
        }
    }

    ///////////////////////////////// GETTERS  /////////////////////////////////

    /** {@inheritdoc} */
    public function getClassName(): string {
        return $this->className;
    }

    /** {@inheritdoc} */
    public function getFileName(): string {
        return $this->fileName;
    }

    /** {@inheritdoc} */
    public function getName(): string {
        return $this->name;
    }

    /** {@inheritdoc} */
    public function getReflector() {
        return $this->reflector;
    }

    /** {@inheritdoc} */
    public function getTag(): TagInterface {
        return $this->tag;
    }

    /** {@inheritdoc} */
    public function getTagName(): string {
        return $this->tag->getName();
    }

    /** {@inheritdoc} */
    public function getTagValue() {
        return $this->tag->getValue();
    }

    /** {@inheritdoc} */
    public function getType(): string {
        return $this->type;
    }

    ///////////////////////////////// Configurators  /////////////////////////////////

    /** {@inheritdoc} */
    public function withTag(TagInterface $tag): AnnotationInterface {
        $clone = clone $this;
        $clone->tag = $tag;
        return $clone;
    }

    /** {@inheritdoc} */
    public function withTagValue($value): AnnotationInterface {
        $clone = clone $this;
        $tag = $clone->tag->withValue($value);
        $clone->tag = $tag;
        return $clone;
    }

    ///////////////////////////////// Exports(cache and json)  /////////////////////////////////

    /** {@inheritdoc} */
    public function jsonSerialize() {

        return [
            'tag' => $this->tag,
            'type' => $this->type,
            'class' => $this->className,
            'name' => $this->name,
            'filename' => $this->fileName,
            'reflector' => get_class($this->reflector)
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
        if (!is_array($array)) throw new RuntimeException('Cannot unserialize, invalid value');

        $this->tag = $array['tag'];
        $this->type = $array['type'];
        $this->className = $array['class'];
        $this->name = $array['name'];
        $this->fileName = $array['filename'];
        $reflector = $array['reflector'];
        if ($reflector == ReflectionClass::class) $this->reflector = new ReflectionClass($this->className);
        else $this->reflector = new $reflector($this->className, $this->name);
    }

}
