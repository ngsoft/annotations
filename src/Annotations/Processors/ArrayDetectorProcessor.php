<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Processors;

use NGSOFT\Interfaces\{
    AnnotationHandlerInterface, AnnotationInterface, AnnotationProcessorInterface
};

class ArrayDetectorProcessor implements AnnotationProcessorInterface {

    public function process(AnnotationInterface $annotation, AnnotationHandlerInterface $handler): AnnotationInterface {
        return $handler->handle($annotation);
    }

}
