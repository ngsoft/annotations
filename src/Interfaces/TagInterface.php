<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use InvalidArgumentException,
    JsonSerializable,
    Serializable;

interface TagInterface extends Serializable, JsonSerializable {

    const VALID_TAG_NAME_REGEX = '/^[a-z][\w\-]+$/i';

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
     * Returns a new instance with given tag name
     * @param string $name
     * @return TagInterface
     * @throws InvalidArgumentException on invalid name
     */
    public function withName(string $name): TagInterface;

    /**
     * Returns a new Instance with given tag value
     * @param mixed $value
     * @return TagInterface
     */
    public function withValue($value): TagInterface;
}
