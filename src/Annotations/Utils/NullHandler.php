<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use NGSOFT\Interfaces\{
    AnnotationHandlerInterface, AnnotationInterface
};

class NullHandler implements AnnotationHandlerInterface {

    public function handle(AnnotationInterface $annotation): AnnotationInterface {

        $input = $annotation->getValue();
        if (is_array($input) and count($input) === 1 and array_key_exists(0, $input)) $annotation = $annotation->withValue($input[0]);


        return $annotation;
    }

}
