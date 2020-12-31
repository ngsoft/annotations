<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use Traversable;

interface AnnotationCollection extends Traversable {

    /**
     * Adds one or many Annotations
     * @param Annotation $annotations
     * @return self
     */
    public function addAnnotation(Annotation ...$annotations): self;

    /**
     * Get Annotation List
     * @return Annotation[]
     */
    public function getAnnotations(): array;
}
