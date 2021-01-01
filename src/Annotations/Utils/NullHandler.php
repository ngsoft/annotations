<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use NGSOFT\Interfaces\{
    AnnotationHandlerInterface, AnnotationInterface
};

class NullHandler implements AnnotationHandlerInterface {

    public function handle(AnnotationInterface $annotation): AnnotationInterface {
        return $annotation;
    }

}
