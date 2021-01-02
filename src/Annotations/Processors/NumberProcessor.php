<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Processors;

use NGSOFT\{
    Annotations\Tags\TagList, Annotations\Tags\TagProperty, Annotations\Utils\Processor, Interfaces\AnnotationInterface,
    Interfaces\TagHandlerInterface, Interfaces\TagInterface, Interfaces\TagProcessorInterface
};
use function mb_strpos;

class NumberProcessor extends Processor implements TagProcessorInterface {

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
            if (preg_match('/^\-?[\d\.]+$/', $tag->getValue())) {
                if (mb_strpos($tag->getValue(), '.') !== false) return $tag->withValue(floatval($tag->getValue()));
                else return $tag->withValue(intval($tag->getValue()));
            }
        }
        return $handler->handle($annotation);
    }

}
