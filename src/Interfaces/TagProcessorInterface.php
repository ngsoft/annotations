<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use NGSOFT\Exceptions\AnnotationException;

interface TagProcessorInterface {

    /**
     * Process Tag
     * @param AnnotationInterface $annotation
     * @param AnnotationHandlerInterface $handler
     * @return AnnotationInterface
     * @throws AnnotationException on Error
     */
    public function process(AnnotationTagInterface $annotation, AnnotationHandlerInterface $handler): AnnotationInterface;
}
