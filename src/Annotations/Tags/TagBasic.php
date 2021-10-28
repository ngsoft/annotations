<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Tags;

use InvalidArgumentException;
use NGSOFT\Interfaces\{
    AnnotationInterface, TagInterface
};

/**
 * This is a Basic Tag
 * You can extends this class to create custom tags
 */
class TagBasic implements TagInterface {

    /** @var string */
    protected $name = '';

    /** @var mixed */
    protected $value = null;

    /** @var string */
    protected $attribute = '';

    /** @var array<string,string|string[]|null> */
    protected $params = [];

    /** @var AnnotationInterface */
    protected $annotation;

    ////////////////////////////   Getters   ////////////////////////////

    /** {@inheritdoc} */
    public function getName(): string {
        return $this->name;
    }

    /** {@inheritdoc} */
    public function getValue() {
        return $this->value;
    }

    /** {@inheritdoc} */
    public function getValues(): array {
        $val = $this->getValue();
        if (!is_array($val)) $val = $val === null ? [] : [$val];
        return $val;
    }

    /** {@inheritdoc} */
    public function getAttribute(): string {
        return $this->attribute;
    }

    /** {@inheritdoc} */
    public function hasAttribute(): bool {
        return !empty($this->attribute);
    }

    /** {@inheritdoc} */
    public function getParams(): array {
        return $this->params;
    }

    /** {@inheritdoc} */
    public function getAnnotation(): AnnotationInterface {
        return $this->annotation;
    }

    ////////////////////////////   Configurator   ////////////////////////////

    /** {@inheritdoc} */
    public function withAnnotation(AnnotationInterface $annotation) {
        $clone = clone $this;
        return $clone->setAnnotation($annotation);
    }

    /** {@inheritdoc} */
    public function withName(string $name) {

        $clone = clone $this;
        return $clone->setName($name);
    }

    /** {@inheritdoc} */
    public function withValue($value) {
        $clone = clone $this;
        return $clone->setValue($value);
    }

    /** {@inheritdoc} */
    public function withAttribute(string $attribute) {

        $clone = clone $this;
        return $clone->setAttribute($attribute);
    }

    /** {@inheritdoc} */
    public function withParams(array $params) {
        $clone = clone $this;
        return $clone->setParams($params);
    }

    ////////////////////////////   Setters   ////////////////////////////

    /**
     * Set Tag Name
     *
     * @param string $name
     * @return static
     */
    protected function setName(string $name) {
        if (!preg_match(self::VALID_TAG_NAME_REGEX, $name)) {
            throw new InvalidArgumentException(sprintf('Invalid tag name "%s".', $name));
        }
        $this->name = $name;
        return $this;
    }

    /**
     * Set Tag Value
     *
     * @param mixed $value
     * @return static
     */
    protected function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * Set Tag attribute name
     *
     * @param string $attribute
     * @return static
     * @throws InvalidArgumentException
     */
    protected function setAttribute(string $attribute) {
        if (
                !preg_match(self::VALID_ATTRIBUTE_REGEX, $attribute)
        ) {
            throw new InvalidArgumentException(sprintf('Attribute "%s" invalid.', $attribute));
        }

        $this->attribute = $attribute;
        return $this;
    }

    /**
     * Set attribute params
     *
     * @param array $params
     * @return static
     */
    protected function setParams(array $params) {
        $this->params = $params;
        return $this;
    }

    /**
     * Set Annotation
     *
     * @param AnnotationInterface $annotation
     * @return static
     */
    protected function setAnnotation(AnnotationInterface $annotation) {
        $this->annotation = $annotation;
        return $this;
    }

    ////////////////////////////   Dump Friendly   ////////////////////////////

    /** {@inheritdoc} */
    public function __debugInfo() {
        $data = $this->jsonSerialize();
        $data['annotationClass'] = $this->annotation ? get_class($this->annotation) : null;
        return $data;
    }

    ////////////////////////////   Cache   ////////////////////////////

    /** {@inheritdoc} */
    public function jsonSerialize() {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'attribute' => $this->attribute,
            'params' => $this->params,
        ];
    }

    /** {@inheritdoc} */
    public function __serialize() {
        return $this->jsonSerialize();
    }

    /** {@inheritdoc} */
    public function __unserialize(array $data) {

        $this
                ->setName($data['name'] ?? '')
                ->setValue($data['value'] ?? null)
                ->setAttribute($data['attribute'] ?? '')
                ->setParams($data['params'] ?? []);
    }

}
