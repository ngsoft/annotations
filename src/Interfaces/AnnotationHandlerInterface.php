<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface AnnotationHandlerInterface {

    /**
     * Handles Next Filter
     * @param AnnotationInterface $annotation
     * @return AnnotationInterface
     */
    public function handle(AnnotationInterface $annotation): AnnotationInterface;
}
