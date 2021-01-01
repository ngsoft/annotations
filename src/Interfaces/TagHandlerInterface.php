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
}
