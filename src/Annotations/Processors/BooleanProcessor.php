<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Processors;

use NGSOFT\{
    Annotations\Tags\TagList, Annotations\Tags\TagProperty, Annotations\Utils\Processor, Interfaces\AnnotationInterface, Interfaces\TagHandlerInterface,
    Interfaces\TagInterface, Interfaces\TagProcessorInterface
};

/**
 * Handles single tags that don't have value (flags) or
 * have values like true, on, false, off
 */
class BooleanProcessor extends Processor implements TagProcessorInterface {

    public function __construct() {
        $this
                ->addIgnoreTagClass(TagProperty::class)
                ->addIgnoreTagClass(TagList::class);
    }

    public function process(AnnotationInterface $annotation, TagHandlerInterface $handler): TagInterface {


        $tag = $annotation->getTag();

        if (
                !$this->isIgnored($tag)
                and is_string($tag->getValue())
        ) {
            if (in_array($tag->getValue(), ['', 'true', 'on'])) return $tag->withValue(true);
            elseif (in_array($tag->getValue(), ['false', 'off'])) return $tag->withValue(true);
        }

        return $handler->handle($annotation);
    }

}
