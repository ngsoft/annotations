<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use InvalidArgumentException;
use NGSOFT\Interfaces\{
    TagInterface, TagProcessorInterface
};

/**
 * Toolkit for Processors
 */
abstract class Processor implements TagProcessorInterface {

    /** @var string[] */
    protected $ignoreTagClasses = [];

    /** @var bool */
    protected $silentMode = false;

    /**
     * Add an ignored tag class
     * @param string $classname
     * @return static
     * @throws InvalidArgumentException
     */
    public function addIgnoreTagClass(string $classname): self {

        if (
                !class_exists($classname)
                or!in_array(TagInterface::class, class_implements($classname))
        ) {
            throw new InvalidArgumentException(sprintf('Invalid class name "%s".', $classname));
        }


        $this->ignoreTagClasses[] = $classname;
        return $this;
    }

    /**
     * Checks if tag implements an ignored class
     * @param TagInterface $tag
     * @return bool
     */
    public function isIgnored(TagInterface $tag): bool {
        foreach ($this->ignoreTagClasses as $className) {
            if ($tag instanceof $className) return true;
        }
        return false;
    }

    /** {@inheritdoc} */
    public function getSilentMode(): bool {
        return $this->silentMode;
    }

    /** {@inheritdoc} */
    public function setSilentMode(bool $silentMode): TagProcessorInterface {
        $this->silentMode = $silentMode;
        return $this;
    }

}
