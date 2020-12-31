<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use NGSOFT\Interfaces\{
    AnnotationHandler, AnnotationInterface, AnnotationProcessor
};

class ProcessorHandler implements AnnotationHandler {

    /** @var AnnotationProcessor */
    private $processor;

    /** @var AnnotationHandler */
    private $next;

    public function __construct(AnnotationProcessor $processor, AnnotationHandler $next) {
        $this->processor = $processor;
        $this->next = $next;
    }

    public function handle(AnnotationInterface $annotation): AnnotationInterface {
        return $this->processor->process($annotation, $this->next);
    }

}
