<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Tags;

use InvalidArgumentException,
    NGSOFT\Interfaces\TagInterface,
    RuntimeException;

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
    public function getParams(): array {
        return $this->params;
    }

    /** {@inheritdoc} */
    public function withName(string $name): TagInterface {
        if (!preg_match(self::VALID_TAG_NAME_REGEX, $name)) {
            throw new InvalidArgumentException(sprintf('Invalid tag name "%s".', $name));
        }
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    /** {@inheritdoc} */
    public function withValue($value): TagInterface {
        $clone = clone $this;
        $clone->value = $value;
        return $clone;
    }

    /** {@inheritdoc} */
    public function withAttribute(string $attribute): TagInterface {
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
    public function withParams(array $params): TagInterface {
        $clone = clone $this;
        $clone->params = $params;
        return $clone;
    }

    /** {@inheritdoc} */
    public function jsonSerialize() {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'attribute' => $this->attribute,
            'params' => $this->params
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
        $this->name = $array['name'];
        $this->value = $array['value'];
        $this->attribute = $array['attribute'];
        $this->params = $array['params'];
    }

}
