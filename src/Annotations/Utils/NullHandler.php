<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use NGSOFT\Interfaces\{
    AnnotationHandler, AnnotationInterface
};

class NullHandler implements AnnotationHandler {

    public function handle(AnnotationInterface $annotation): AnnotationInterface {
        return $annotation;
    }

}
