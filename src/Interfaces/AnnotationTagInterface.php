<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface AnnotationTagInterface extends \Serializable, \JsonSerializable {

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
     *
     * @param type $value
     * @return AnnotationTagInterface
     */
    public function withValue($value): AnnotationTagInterface;
}
