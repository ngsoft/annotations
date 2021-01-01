<?php

namespace NGSOFT\Annotations\Processors;

class TypeHintingProcessor implements \NGSOFT\Interfaces\AnnotationProcessorInterface {

    const RESERVED_KEYWORDS = [
        //gettype
        'boolean', 'integer', 'double', 'string', 'array', 'object', 'resource', 'NULL',
        //aliases
        'bool', 'int', 'float', 'void', 'iterable', 'null', 'mixed'
    ];

    public function process(\NGSOFT\Interfaces\AnnotationInterface $annotation, \NGSOFT\Interfaces\AnnotationHandlerInterface $handler): \NGSOFT\Interfaces\AnnotationInterface {

    }

}
