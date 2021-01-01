<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use NGSOFT\Interfaces\{
    AnnotationInterface, TagHandlerInterface, TagInterface, TagProcessorInterface
};

class ProcessorHandler implements TagHandlerInterface {

    /** @var TagProcessorInterface */
    private $processor;

    /** @var TagHandlerInterface */
    private $next;

    public function __construct(TagProcessorInterface $processor, TagHandlerInterface $next) {
        $this->processor = $processor;
        $this->next = $next;
    }

    /** {@inheritdoc} */
    public function handle(AnnotationInterface $annotation): TagInterface {
        return $this->processor->process($annotation, $this->next);
    }

}
