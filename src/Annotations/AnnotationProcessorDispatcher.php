<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use NGSOFT\{
    Annotations\Utils\NullHandler, Interfaces\AnnotationHandler, Interfaces\AnnotationInterface, Interfaces\AnnotationProcessor,
    Interfaces\AnnotationProcessorDispatcher
};

class AnnotationProcessorDispatcher {

    const DEFAULT_PROCESSORS = [];

    /** @var AnnotationHandler */
    private $stack;

    /**
     * @param AnnotationProcessor[]|null $processors
     * @throws RuntimeException
     */
    public function __construct(
            ?array $processors = null
    ) {
        $this->stack = new NullHandler();
        if (!is_array($processors)) $processors = self::DEFAULT_PROCESSORS;
        foreach ($processors as $processor) {

            if (
                    !($processor instanceof AnnotationProcessor)
            ) throw new RuntimeException('Invalid AnnotationProcessor.');
        }
    }

    /**
     * Handles Single Annotation Processing
     * @param AnnotationInterface $annotation
     * @return AnnotationInterface
     */
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
