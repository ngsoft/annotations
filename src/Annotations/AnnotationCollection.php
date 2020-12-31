<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use ArrayIterator,
    IteratorAggregate;
use NGSOFT\Interfaces\{
    AnnotationCollectionInterface, AnnotationInterface
};

class AnnotationCollection implements AnnotationCollectionInterface, IteratorAggregate {

    /** @var AnnotationInterface[] */
    private $annotations = [];

    /** {@inheritdoc} */
    public function addAnnotation(AnnotationInterface ...$annotations): AnnotationCollectionInterface {
        foreach ($annotations as $annotation) {
            if (!in_array($annotation, $this->annotations)) {
                $this->annotations[] = $annotation;
            }
        }
        return $this;
    }

    /** {@inheritdoc} */
    public function getAnnotations(): array {
        return $this->annotations;
    }

    /** {@inheritdoc} */
    public function getIterator() {
        return new ArrayIterator($this->getAnnotations());
    }

    /** {@inheritdoc} */
    public function count() {
        return count($this->annotations);
    }

}
