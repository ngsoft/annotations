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

    /** {@inheritdoc} */
    public function getName(): string {
        return $this->name;
    }

    /** {@inheritdoc} */
    public function getValue() {
        return $this->value;
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

    /** {@inheritdoc} */
    public function withAnnotation(AnnotationInterface $annotation) {
        $clone = clone $this;
        $clone->annotation = $annotation;
        return $clone;
    }

    /** {@inheritdoc} */
    public function withName(string $name) {
        if (!preg_match(self::VALID_TAG_NAME_REGEX, $name)) {
            throw new InvalidArgumentException(sprintf('Invalid tag name "%s".', $name));
        }
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    /** {@inheritdoc} */
    public function withValue($value) {
        $clone = clone $this;
        $clone->value = $value;
        return $clone;
    }

    /** {@inheritdoc} */
    public function withAttribute(string $attribute) {
        if (
                !preg_match(self::VALID_ATTRIBUTE_REGEX, $attribute)
        ) {
            throw new InvalidArgumentException(sprintf('Attribute "%s" invalid.', $attribute));
        }

        $clone = clone $this;
        $clone->attribute = $attribute;
        return $clone;
    }

    /** {@inheritdoc} */
    public function withParams(array $params) {
        $clone = clone $this;
        $clone->params = $params;
        return $clone;
    }

    ////////////////////////////   Dump Friendly   ////////////////////////////

    /** {@inheritdoc} */
    public function __debugInfo() {
        $data = $this->jsonSerialize();
        $data['annotationClass'] = $this->annotation ? get_class($this->annotation) : null;
        return $data;
    }

    ////////////////////////////   CacheAble   ////////////////////////////

    /** {@inheritdoc} */
    public static function __set_state($array) {
        $i = new static();
        $i->name = $array['name'] ?? '';
        $i->value = $array['value'] ?? null;
        $i->attribute = $array['attribute'] ?? '';
        $i->params = $array['params'] ?? [];
        return $i;
    }

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
        $this->name = $data['name'] ?? '';
        $this->value = $data['value'] ?? null;
        $this->attribute = $data['attribute'] ?? '';
        $this->params = $data['params'] ?? [];
    }

}
