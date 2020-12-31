<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface AnnotationHandler {

    /**
     * Handles Next Filter
     * @param Annotation $annotation
     * @return Annotation
     */
    public function handle(Annotation $annotation): Annotation;
}
