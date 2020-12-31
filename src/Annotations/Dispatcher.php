<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use NGSOFT\{
    Annotations\Utils\NullHandler, Interfaces\AnnotationHandler, Interfaces\AnnotationInterface, Interfaces\AnnotationProcessor,
    Interfaces\AnnotationProcessorDispatcher
};

class Dispatcher implements AnnotationProcessorDispatcher {

    /** @var AnnotationHandler */
    private $stack;

    public function __construct() {
        $this->stack = new NullHandler();
    }

    /** {@inheritdoc} */
    public function handle(AnnotationInterface $annotation): AnnotationInterface {
        return $this->stack->handle($annotation);
    }

    /** {@inheritdoc} */
    public function addProcessor(AnnotationProcessor $processor): AnnotationProcessorDispatcher {
        $next = $this->stack;
        $this->stack = new Utils\ProcessorHandler($processor, $next);

        return $this;
    }

}
