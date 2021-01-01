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
}
