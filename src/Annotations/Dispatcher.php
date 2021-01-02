<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use NGSOFT\{
    Annotations\Processors\BooleanProcessor, Annotations\Processors\ListProcessor, Annotations\Processors\NumberProcessor,
    Annotations\Processors\TypeHintingProcessor, Annotations\Utils\NullHandler, Annotations\Utils\ProcessorHandler,
    Interfaces\AnnotationInterface, Interfaces\TagHandlerInterface, Interfaces\TagProcessorInterface
};
use RuntimeException;

class Dispatcher {

    const DEFAULT_PROCESSORS = [
        NumberProcessor::class,
        BooleanProcessor::class,
        TypeHintingProcessor::class,
        // to run first (Last position)
        ListProcessor::class
    ];

    /** @var TagHandlerInterface */
    protected $stack;

    /** @var bool */
    protected $silentMode = false;

    /**
     * @param TagProcessorInterface[]|null $processors List of Processors to use
     * @param bool|null $silentMode Enable or disable Silent Mode
     * @param TagHandlerInterface|null $kernel Last to be executed Handler (defaults to NullHandler)
     * @throws RuntimeException
     */
    public function __construct(
            ?array $processors = null,
            ?bool $silentMode = null,
            ?TagHandlerInterface $kernel = null
    ) {
        //add the last processor to the stack
        if ($kernel !== null) $this->stack = $kernel;
        else $this->stack = new NullHandler();
        if (is_bool($silentMode)) $this->silentMode = $silentMode;
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
        $this->stack->setSilentMode($this->silentMode);
        $tag = $this->stack->handle($annotation);
        return $annotation->withTag($tag);
    }

    /**
     * Add a processor on top of the stack
     * @param TagProcessorInterface $processor
     * @return Dispatcher
     */
    public function addProcessor(TagProcessorInterface $processor): self {
        $next = $this->stack;
        $this->stack = new ProcessorHandler($processor, $next);

        return $this;
    }

    /**
     * Get Silent Mode Value
     * When silent mode is enabled Processors will not throw exceptions
     * but annotations results can be different as expected
     *
     * @return bool
     */
    public function getSilentMode(): bool {
        return $this->silentMode;
    }

    /**
     * Set Silent Mode Value
     * @param bool $silentMode
     * @return static
     */
    public function setSilentMode(bool $silentMode): self {
        $this->silentMode = $silentMode;
        return $this;
    }

}
