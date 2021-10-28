<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Types;

use InvalidArgumentException;
use NGSOFT\Interfaces\{
    AnnotationInterface, TagInterface
};
use ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

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
        $this->tag = $tag->withAnnotation($this);
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
        return $clone->setTag($tag);
    }

    /** {@inheritdoc} */
    public function withTagValue($value): AnnotationInterface {
        $clone = clone $this;
        $tag = $clone->tag->withValue($value);
        return $clone->setTag($tag);
    }

    ////////////////////////////   Setters   ////////////////////////////

    /**
     * Set Tag
     *
     * @param TagInterface $tag
     * @return static
     */
    protected function setTag(TagInterface $tag) {
        $this->tag = $tag;
        return $this;
    }

    /**
     * Set Class Name
     *
     * @param string $className
     * @return static
     */
    protected function setClassName(string $className) {
        $this->className = $className;
        return $this;
    }

    /**
     * Set File Name
     *
     * @param string $fileName
     * @return static
     */
    protected function setFileName(string $fileName) {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     *
     *
     * @param string $name
     * @return static
     */
    protected function setName(string $name) {
        $this->name = $name;
        return $this;
    }

    /**
     *
     * @param string $type
     * @return static
     */
    protected function setType(string $type) {
        $this->type = $type;
        return $this;
    }

    ////////////////////////////   Dump Friendly   ////////////////////////////

    /** {@inheritdoc} */
    public function __debugInfo() {
        return $this->jsonSerialize();
    }

    ///////////////////////////////// Exports(cache and json)  /////////////////////////////////

    /** {@inheritdoc} */
    public function jsonSerialize() {

        return [
            'tag' => $this->getTag(),
            'type' => $this->getType(),
            'className' => $this->getClassName(),
            'name' => $this->getName(),
            'fileName' => $this->getFileName(),
            'reflector' => get_class($this->getReflector())
        ];
    }

    /** {@inheritdoc} */
    public function __serialize() {
        return $this->jsonSerialize();
    }

    /** {@inheritdoc} */
    public function __unserialize(array $data) {


        $this
                ->setTag($data['tag'])
                ->setType($data['type'])
                ->setClassName($data['className'])
                ->setName($data['name'])
                ->setFileName($data['fileName']);

        $reflector = $data['reflector'];

        if ($reflector == ReflectionClass::class) $this->reflector = new ReflectionClass($this->className);
        else $this->reflector = new $reflector($this->className, $this->name);
        $this->tag = $this->tag->withAnnotation($this);
    }

}
