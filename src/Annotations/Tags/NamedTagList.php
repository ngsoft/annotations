<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Tags;

use NGSOFT\Exceptions\AnnotationException;

/**
 * This is a Taglist that uses strings as keys
 */
class NamedTagList extends TagList {

    protected function getValidParams(): array {
        return [];
    }

    /** {@inheritdoc} */
    protected function setValue($value) {
        $value = $value ?? [];

        if (
                is_array($value) and
                !empty($valid = $this->getValidParams())
        ) {
            foreach (array_keys($value) as $key) {
                if (!in_array($key, $valid)) {
                    throw new AnnotationException(
                                    $this->getAnnotation(),
                                    sprintf('Invalid annotation value for "@%s" in file "%s"', $this->getName(), $this->getAnnotation()->getFileName())
                    );
                }

                if (property_exists($this, $key)) {
                    $this->{$key} = $value[$key];
                }
            }
        }
        $this->value = $value;
        return $this;
    }

}
