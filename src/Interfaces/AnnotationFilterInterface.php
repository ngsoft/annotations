<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface AnnotationFilterInterface {

    /**
     * Defines if annotation should be returned
     *
     * @param AnnotationInterface $annotation
     * @return bool
     */
    public function filter(AnnotationInterface $annotation): bool;
}
