<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

use Countable,
    Traversable;

interface AnnotationCollectionInterface extends Traversable, Countable {

    /**
     * Adds one or many Annotations
     * @param AnnotationInterface $annotations
     * @return self
     */
    public function addAnnotation(AnnotationInterface ...$annotations): self;

    /**
     * Get Annotation List
     * @return AnnotationInterface[]
     */
    public function getAnnotations(): array;
}
