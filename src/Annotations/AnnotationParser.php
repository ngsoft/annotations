<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use NGSOFT\Interfaces\{
    AnnotationCollectionInterface, AnnotationFactoryInterface, AnnotationParserInterface, AnnotationProcessorDispatcher,
    AnnotationProcessorStack
};
use ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

class AnnotationParser implements AnnotationParserInterface {

    /** @var AnnotationFactoryInterface */
    private $annotationFactory;

    /** @var AnnotationProcessorStack */
    private $processorStack;

    /** @var AnnotationProcessorDispatcher */
    private $processorDispatcher;

    public function __construct(
            AnnotationFactoryInterface $annotationFactory = null,
            AnnotationProcessorStack $processorStack = null,
            AnnotationProcessorDispatcher $processorDispatcher = null
    ) {

        if (!($annotationFactory instanceof AnnotationFactoryInterface)) {
            $annotationFactory = new AnnotationFactory();
        }
        if (!($processorStack instanceof AnnotationProcessorStack)) {
            $processorStack = new ProcessorStack();
        }
        if (!($processorDispatcher instanceof AnnotationProcessorDispatcher)) {
            $processorDispatcher = new Dispatcher();
        }
        $this->annotationFactory = $annotationFactory;
        $this->processorStack = $processorStack;
        $this->processorDispatcher = $processorDispatcher;
    }

    /** {@inheritdoc} */
    public function parseAnnotation(string $docComment): array {

    }

    public function parseClass(ReflectionClass $reflector): AnnotationCollectionInterface {

    }

    /** {@inheritdoc} */
    public function parseClassName(string $className): AnnotationCollectionInterface {

        if (!class_exists($className)) {
            throw new \InvalidArgumentException(sprintf('Invalid Class %s', $className));
        }

        return $this->parseClass(new \ReflectionClass($className));
    }

    public function parseMethod(ReflectionMethod $reflector): AnnotationCollectionInterface {

    }

    public function parseProperty(ReflectionProperty $reflector): AnnotationCollectionInterface {

    }

}
