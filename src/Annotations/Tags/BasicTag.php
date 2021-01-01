<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Tags;

class BasicTag implements \NGSOFT\Interfaces\AnnotationTagInterface {

    /** @var string */
    protected $name = '';

    /** @var mixed */
    protected $value = null;

    public function getName(): string {
        return $this->name;
    }

    public function getValue() {
        return $this->value;
    }

    public function withName(string $name): \NGSOFT\Interfaces\AnnotationTagInterface {
        if (!preg_match(self::VALID_TAG_NAME_REGEX, $name)) {
            throw new InvalidArgumentException(sprintf('Invalid tag name "%s".', $name));
        }
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    /** {@inheritdoc} */
    public function withValue($value): \NGSOFT\Interfaces\AnnotationTagInterface {
        $clone = clone $this;
        $clone->value = $value;
        return $clone;
    }

    /** {@inheritdoc} */
    public function jsonSerialize() {
        return [
            'name' => $this->name,
            'value' => $this->value
        ];
    }

    /** {@inheritdoc} */
    public function serialize() {
        $array = $this->jsonSerialize();
        return \serialize($array);
    }

    /** {@inheritdoc} */
    public function unserialize($serialized) {
        $array = $this->unserialize($serialized);
        if (!is_array($array)) throw new RuntimeException('Cannot unserialize, invalid value');
        $this->name = $array['name'];
        $this->value = $array['value'];
    }

}
