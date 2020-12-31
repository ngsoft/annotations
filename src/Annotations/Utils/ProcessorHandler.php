<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use NGSOFT\Interfaces\{
    AnnotationHandlerInterface, AnnotationInterface, AnnotationProcessorInterface
};

class ProcessorHandler implements AnnotationHandlerInterface {

    /** @var AnnotationProcessorInterface */
    private $processor;

    /** @var AnnotationHandlerInterface */
    private $next;

    public function __construct(AnnotationProcessorInterface $processor, AnnotationHandlerInterface $next) {
        $this->processor = $processor;
        $this->next = $next;
    }

    public function handle(AnnotationInterface $annotation): AnnotationInterface {
        return $this->processor->process($annotation, $this->next);
    }

}
