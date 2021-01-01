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
    protected $attributeName = '';

    /**
     * Get then Named Attribute
     * @return string
     */
    public function getAttributeName(): string {
        return $this->attributeName;
    }

    /**
     * Creates a new instance with given attribute name
     * @param string $attributeName
     * @return static
     * @throws InvalidArgumentException
     */
    public function withAttributeName(string $attributeName): self {
        if (
                !empty($attributeName)
                and!preg_match(self::VALID_ATTRIBUTE_REGEX, $attributeName)
        ) {
            throw new InvalidArgumentException(sprintf('Attribute name "%s" invalid.', $attributeName));
        }
        $clone = clone $this;
        $clone->attributeName = $attributeName;
        return $clone;
    }

    /** {@inheritdoc} */
    public function jsonSerialize() {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'attribute' => $this->attributeName,
        ];
    }

    /** {@inheritdoc} */
    public function unserialize($serialized) {
        $array = \unserialize($serialized);
        if (!is_array($array)) throw new RuntimeException('Cannot unserialize, invalid value');
        $this->name = $array['name'];
        $this->value = $array['value'];
        $this->attributeName = $array['attribute'];
    }

}
