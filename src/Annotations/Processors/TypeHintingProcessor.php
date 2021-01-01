<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Processors;

use NGSOFT\Interfaces\{
    AnnotationInterface, TagHandlerInterface, TagInterface, TagProcessorInterface
};

/**
 * @method type methodName(type $paramName) Description
 * @property type $name Description
 */
class TypeHintingProcessor implements TagProcessorInterface {

    const RESERVED_KEYWORDS = [
        //gettype
        'boolean', 'integer', 'double', 'string', 'array', 'object', 'resource', 'NULL',
        //aliases
        'bool', 'int', 'float', 'void', 'iterable', 'null', 'mixed'
    ];
    const TAGS_WITH_PARAMS = [
        'var', 'param'
    ];
    const TAGS_WITHOUT_PARAMS = [
        'return'
    ];
    const TAG_HEADERS = [
        'property', 'property-read', 'property-write', 'method'
    ];

    public function process(AnnotationInterface $annotation, TagHandlerInterface $handler): TagInterface {


        if ($annotation->getTag() instanceof \NGSOFT\Annotations\Tags\TagProperty) {
            $tag = $annotation->getTag();

            var_dump($tag);
        }




        return $handler->handle($annotation);
    }

}
