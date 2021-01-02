<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Tags;

use InvalidArgumentException,
    RuntimeException;

/**
 * Tag that uses type hinting (var, return, method ...)
 * Extends that class to detect type hintings
 */
class TagProperty extends TagBasic {

    const VALID_ATTRIBUTE_REGEX = '/^[a-z]\w+$/i';

    /** @var string */
    protected $attribute = '';

    /** @var array<string,string|string[]> */
    protected $params = [];

    /**
     * Get Params for Attribute
     * @return array<string,string|string[]>
     */
    public function getParams(): array {
        return $this->params;
    }

    /**
     * Creates a new instance with given params
     * @return static
     */
    public function withParams(array $params): self {
        $clone = clone $this;
        $clone->params = $params;
        return $clone;
    }

    /**
     * Get then Named Attribute
     * @return string
     */
    public function getAttribute(): string {
        return $this->attributeName;
    }

    /**
     * Creates a new instance with given attribute name
     * @param string $attributeName
     * @return static
     * @throws InvalidArgumentException
     */
    public function withAttribute(string $attributeName): self {
        if (
                !empty($attributeName)
                and!preg_match(self::VALID_ATTRIBUTE_REGEX, $attributeName)
        ) {
            throw new InvalidArgumentException(sprintf('Attribute name "%s" invalid.', $attributeName));
        }
        $clone = clone $this;
        $clone->attribute = $attributeName;
        return $clone;
    }

    /** {@inheritdoc} */
    public function jsonSerialize() {

        return array_merge(parent::jsonSerialize(), [
            'attribute' => $this->attribute,
            'params' => $this->params,
        ]);
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
