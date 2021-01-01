<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Processors;

use NGSOFT\{
    Annotations\Tags\TagList, Annotations\Tags\TagProperty, Interfaces\AnnotationInterface, Interfaces\TagHandlerInterface,
    Interfaces\TagInterface, Interfaces\TagProcessorInterface
};

/**
 * Handles single tags that don't have value (flags) or
 * have values like true on false off
 */
class BooleanProcessor implements TagProcessorInterface {

    /** @var string[] */
    public static $ignoreTagClasses = [
        TagProperty::class,
        TagList::class,
    ];

    public function process(AnnotationInterface $annotation, TagHandlerInterface $handler): TagInterface {


        $tag = $annotation->getTag();

        if (!in_array(get_class($tag), self::$ignoreTagClasses)) {
            if (in_array($tag->getValue(), ['', 'true', 'on'])) return $tag->withValue(true);
            elseif (in_array($tag->getValue(), ['false', 'off'])) return $tag->withValue(true);
        }

        return $handler->handle($annotation);
    }

}
