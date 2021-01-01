<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use InvalidArgumentException,
    JsonSerializable,
    Serializable;

interface AnnotationTagInterface extends Serializable, JsonSerializable {

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
     * @return AnnotationTagInterface
     * @throws InvalidArgumentException on invalid name
     */
    public function withName(string $name): AnnotationTagInterface;

    /**
     * Returns a new Instance with given tag value
     * @param mixed $value
     * @return AnnotationTagInterface
     */
    public function withValue($value): AnnotationTagInterface;
}
