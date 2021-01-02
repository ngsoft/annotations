<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use NGSOFT\{
    Annotations\Processors\BooleanProcessor, Annotations\Processors\ListProcessor, Annotations\Processors\NumberProcessor,
    Annotations\Processors\TypeHintingProcessor, Annotations\TagProcessorDispatcher, Annotations\Utils\NullHandler,
    Annotations\Utils\ProcessorHandler, Interfaces\AnnotationInterface, Interfaces\TagHandlerInterface,
    Interfaces\TagProcessorInterface
};
use RuntimeException;

class TagProcessorDispatcher {

    const DEFAULT_PROCESSORS = [
        NumberProcessor::class,
        BooleanProcessor::class,
        TypeHintingProcessor::class,
        // to run first (Last position)
        ListProcessor::class
    ];

    /** @var TagHandlerInterface */
    private $stack;

    /**
     * @param TagProcessorInterface[]|null $processors
     * @throws RuntimeException
     */
    public function __construct(
            ?array $processors = null
    ) {
        $this->stack = new NullHandler();
        if (!is_array($processors)) $processors = array_map(fn($classname) => new $classname(), self::DEFAULT_PROCESSORS);
        foreach ($processors as $processor) {
            if (
                    !($processor instanceof TagProcessorInterface)
            ) throw new RuntimeException('Invalid Processor.');
            $this->addProcessor($processor);
        }
    }

    /**
     * Handles Single Annotation Processing
     * @param AnnotationInterface $annotation
     * @return AnnotationInterface
     */
    public function handle(AnnotationInterface $annotation): AnnotationInterface {
        $tag = $this->stack->handle($annotation);
        return $annotation->withTag($tag);
    }

    /** {@inheritdoc} */
    public function addProcessor(TagProcessorInterface $processor): TagProcessorDispatcher {
        $next = $this->stack;
        $this->stack = new ProcessorHandler($processor, $next);

        return $this;
    }

}
