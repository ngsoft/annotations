<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use NGSOFT\Exceptions\AnnotationException;

interface TagProcessorInterface {

    /**
     * Process Tag
     * @param AnnotationInterface $annotation
     * @param TagHandlerInterface $handler
     * @return TagInterface
     * @throws AnnotationException on Error
     */
    public function process(AnnotationInterface $annotation, TagHandlerInterface $handler): TagInterface;

    /**
     * Set Silent Mode
     * @param bool $silentMode if set to true Processor will not throw exception on error, it will pass
     * @return static
     */
    public function setSilentMode(bool $silentMode): self;

    /**
     * Get Silent Mode
     * @return bool
     */
    public function getSilentMode(): bool;
}
