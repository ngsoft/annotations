<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use NGSOFT\Interfaces\AnnotationProcessorDispatcher;

class Dispatcher implements AnnotationProcessorDispatcher {

    public function add(string $className): \NGSOFT\Interfaces\AnnotationProcessorDispatcher {

    }

    public function addProcessor(\NGSOFT\Interfaces\AnnotationProcessor $processor): \NGSOFT\Interfaces\AnnotationProcessorDispatcher {

    }

}
