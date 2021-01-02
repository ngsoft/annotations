<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use InvalidArgumentException,
    NGSOFT\Interfaces\TagInterface;

/**
 * Toolkit for Processors
 */
trait ProcessorTrait {

    /** @var string[] */
    protected $ignoreTagClasses = [];

    /** @var bool */
    protected $ignoreErrors = false;

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

    /**
     * Set Ignore Errors Flag
     * @param bool $ignoreErrors if set to true Processor will not throw exception on error, it will pass
     * @return static
     */
    public function setIgnoreErrors(bool $ignoreErrors): self {
        $this->ignoreErrors = $ignoreErrors;
        return $this;
    }

    /**
     * Get Ignore Errors Flag
     * @return bool
     */
    public function getIgnoreErrors(): bool {
        return $this->ignoreErrors;
    }

}
