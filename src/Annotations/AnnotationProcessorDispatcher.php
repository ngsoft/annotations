<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use NGSOFT\{
    Annotations\Utils\NullHandler, Annotations\Utils\ProcessorHandler, Interfaces\AnnotationHandlerInterface,
    Interfaces\AnnotationInterface, Interfaces\AnnotationProcessorInterface
};
use RuntimeException;

class AnnotationProcessorDispatcher {

    const DEFAULT_PROCESSORS = [];

    /** @var AnnotationHandlerInterface */
    private $stack;

    /**
     * @param AnnotationProcessorInterface[]|null $processors
     * @throws RuntimeException
     */
    public function __construct(
            ?array $processors = null
    ) {
        $this->stack = new NullHandler();
        if (!is_array($processors)) $processors = array_map(fn($classname) => new $classname(), self::DEFAULT_PROCESSORS);
        foreach ($processors as $processor) {
            if (
                    !($processor instanceof AnnotationProcessorInterface)
            ) throw new RuntimeException('Invalid AnnotationProcessor.');
            $this->addProcessor($processor);
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
    public function addProcessor(AnnotationProcessorInterface $processor): AnnotationProcessorDispatcher {
        $next = $this->stack;
        $this->stack = new ProcessorHandler($processor, $next);

        return $this;
    }

}
