<?php

namespace NGSOFT\Annotations\Tags;

/**
 * Type Hinting for List Tags
 * Initialized by ArrayDetectorProcessor
 */
class TagList extends TagBasic {

    public function __construct(string $name, array $value) {
        $this->name = $name;
        $this->value = $value;
    }

}
