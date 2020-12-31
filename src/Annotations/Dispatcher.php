<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use NGSOFT\Interfaces\{
    AnnotationInterface, AnnotationProcessor, AnnotationProcessorDispatcher
};

class Dispatcher implements AnnotationProcessorDispatcher {

    public function handle(AnnotationInterface $annotation): AnnotationInterface {

    }

    public function addProcessor(AnnotationProcessor $processor): AnnotationProcessorDispatcher {

    }

}
