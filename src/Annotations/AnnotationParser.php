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

    const IGNORE_TAGS = [
        'inheritdoc'
    ];

    /** @var AnnotationFactoryInterface */
    private $annotationFactory;

    /** @var AnnotationProcessorStack */
    private $processorStack;

    /** @var AnnotationProcessorDispatcher */
    private $processorDispatcher;

    public function __construct(
            ?AnnotationProcessorStack $processorStack = null
    ) {
        if (!($processorStack instanceof AnnotationProcessorStack)) {
            $processorStack = new ProcessorStack();
        }
        $this->processorStack = $processorStack;
        $this->annotationFactory = new AnnotationFactory();
        $this->processorDispatcher = new Dispatcher();

        foreach ($this->processorStack as $processor) {
            $this->processorDispatcher->addProcessor($processor);
        }
    }

    /** {@inheritdoc} */
    public function parseAnnotation(string $docComment): array {
        $result = [];

        $lines = explode("\n", $docComment);

        foreach ($lines as $line) {
            $line = trim($line);
            $line = trim($line, '/*');
            $pos = mb_strpos($line, '@');

            if ($pos !== false) {
                //annotation there
                $line = mb_substr($line, $pos);
                if (preg_match('/^@(\w[\w-]+)\h?+/', $line, $matches) > 0) {
                    $tag = $matches[1];
                    if (in_array($tag, self::IGNORE_TAGS)) continue;
                    $len = mb_strlen($tag) + 1;
                    $line = mb_substr($line, $len);
                    $line = trim($line);
                    if (!isset($result[$tag])) $result[$tag] = [];

                    $result[$tag][] = $line;
                }
            }
        }






        var_dump($result);



        return $result;
    }

    /** {@inheritdoc} */
    public function parseClass(ReflectionClass $reflector): AnnotationCollectionInterface {

        //first class Doc Comment
        $collection = $this->singleDocCommentParser($reflector);

        foreach ($reflector->getProperties() as $propReflector) {
            $tmp = $this->parseProperty($propReflector);
            if (count($tmp) > 0) $collection->addAnnotation(...$tmp->getAnnotations());
        }
        foreach ($reflector->getMethods() as $methodReflector) {
            $tmp = $this->parseMethod($methodReflector);
            if (count($tmp) > 0) $collection->addAnnotation(...$tmp->getAnnotations());
        }
        return $collection;
    }

    /** {@inheritdoc} */
    public function parseClassName(string $className): AnnotationCollectionInterface {

        if (!class_exists($className)and!interface_exists($className)) {
            throw new \InvalidArgumentException(sprintf('Invalid Class %s', $className));
        }

        return $this->parseClass(new \ReflectionClass($className));
    }

    /** {@inheritdoc} */
    public function parseMethod(ReflectionMethod $reflector): AnnotationCollectionInterface {
        return $this->singleDocCommentParser($reflector);
    }

    /** {@inheritdoc} */
    public function parseProperty(ReflectionProperty $reflector): AnnotationCollectionInterface {
        return $this->singleDocCommentParser($reflector);
    }

    /**
     * @param ReflectionProperty|ReflectionMethod|ReflectionClass $reflector
     * @return AnnotationCollectionInterface
     */
    private function singleDocCommentParser($reflector): AnnotationCollectionInterface {
        $collection = $this->annotationFactory->createAnnotationCollection();
        if (($docComment = $reflector->getDocComment()) !== false) {
            $parsed = $this->parseAnnotation($docComment);
            if (count($parsed) > 0) {
                foreach ($parsed as $tag => $value) {
                    $annotation = $this->processorDispatcher->handle($this->annotationFactory->createAnnotation($reflector, $tag, $value));
                    $collection->addAnnotation($annotation);
                }
            }
        }
        return $collection;
    }

}
