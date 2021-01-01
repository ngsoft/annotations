<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Processors;

use NGSOFT\{
    Annotations\Tags\TagList, Annotations\Tags\TagProperty, Interfaces\AnnotationInterface, Interfaces\TagHandlerInterface,
    Interfaces\TagInterface, Interfaces\TagProcessorInterface
};

class NumberProcessor implements TagProcessorInterface {

    /** @var string[] */
    public static $ignoreTagClasses = [
        TagProperty::class,
        TagList::class,
    ];

    public function process(AnnotationInterface $annotation, TagHandlerInterface $handler): TagInterface {

        $tag = $annotation->getTag();

        if (!in_array(get_class($tag), self::$ignoreTagClasses)) {
            if (preg_match('/^\-?[\d\.]+$/', $tag->getValue())) {
                if (mb_strpos($tag->getValue(), '.') !== false) return $tag->withValue(floatval($tag->getValue()));
                else return $tag->withValue(intval($tag->getValue()));
            }
        }
        return $handler->handle($annotation);
    }

}
