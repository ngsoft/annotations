<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use InvalidArgumentException,
    JsonSerializable,
    Serializable;

interface TagInterface extends Serializable, JsonSerializable {

    /**
     * Tag Name
     */
    const VALID_TAG_NAME_REGEX = '/^[a-z][\w\-]+$/i';

    /**
     * Attribute is a sub named parameter
     * Can be a variable or a method name and can be empty string
     */
    const VALID_ATTRIBUTE_REGEX = '/^(|[a-z]\w+)$/i';

    /**
     * Get Annotation Tag Name
     * @return string
     */
    public function getName(): string;

    /**
     * Get Annotation Parsed Value
     * @return mixed
     */
    public function getValue();

    /**
     * Get Attribute name
     * @return string
     */
    public function getAttribute(): string;

    /**
     * Get Params for Attribute
     * @return array
     */
    public function getParams(): array;

    /**
     * Returns a new instance with given tag name
     * @param string $name
     * @return TagInterface
     * @throws InvalidArgumentException on invalid name
     */
    public function withName(string $name): self;

    /**
     * Returns a new Instance with given tag value
     * @param mixed $value
     * @return static
     */
    public function withValue($value): self;

    /**
     * Creates a new instance with given attribute
     * @param string $attribute
     * @return static
     * @throws InvalidArgumentException if invalid attribute
     */
    public function withAttribute(string $attribute): self;

    /**
     * Creates a new instance with given params
     * @param array $params
     * @return static
     */
    public function withParams(array $params): self;
}
