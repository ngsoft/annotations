<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface TagHandlerInterface {

    /**
     * Handles Next Filter
     * @param AnnotationInterface $annotation
     * @return TagInterface
     */
    public function handle(AnnotationInterface $annotation): TagInterface;

    /**
     * Set Silent Mode
     * @param bool $silentMode if set to true Processor will not throw exception on error, it will pass
     * @return static
     */
    public function setSilentMode(bool $silentMode): self;
}
