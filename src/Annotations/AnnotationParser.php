<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use InvalidArgumentException;
use NGSOFT\Interfaces\{
    AnnotationFactoryInterface, AnnotationInterface
};
use ReflectionClass,
    ReflectionException,
    ReflectionMethod,
    ReflectionProperty;
use function mb_internal_encoding,
             mb_strlen,
             mb_strpos,
             mb_substr;

mb_internal_encoding("UTF-8");

//$fileinfo = new SplFileInfo($refl->getFileName());
//using modified time to miss on modified model to reload new metadatas
//$key = md5($fileinfo->getMTime() . $fileinfo->getPathname());

class AnnotationParser {

    const VERSION = '1.0';
    const DEFAULT_IGNORE_TAGS = [
        'inheritdoc', 'phan'
    ];

    /** @var AnnotationFactoryInterface */
    private $annotationFactory;

    /** @var AnnotationProcessorDispatcher */
    private $processorDispatcher;

    /** @var string[] */
    private $ignoreTags = [];

    public function __construct(
            ?AnnotationProcessorDispatcher $processorDispatcher = null,
            ?AnnotationFactoryInterface $annotationFactory = null
    ) {
        $this->ignoreTags = self::DEFAULT_IGNORE_TAGS;
        if ($annotationFactory instanceof AnnotationFactoryInterface) $this->annotationFactory = $annotationFactory;
        $this->annotationFactory = new AnnotationFactory();
        if ($processorDispatcher instanceof AnnotationProcessorDispatcher) $this->processorDispatcher = $processorDispatcher;
        else $this->processorDispatcher = new AnnotationProcessorDispatcher();
    }

    /**
     * Blacklist one to many tags
     * @param string ...$tags
     * @return self
     */
    public function ignoreTags(string ...$tags): self {
        $this->ignoreTags = array_merge($this->ignoreTags, $tags);
        return $this;
    }

    /**
     * Parse a doc block and returns parsed values
     * @param string $docComment
     * @param string[] $tags Tags to find (empty tags will returns all)
     * @return array<string,string[]>
     */
    public function parseAnnotation(string $docComment, array $tags = []): array {

        $whitelist = !empty($tags);

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
                    if (!$whitelist and in_array($tag, $this->ignoreTags)) continue;
                    if ($whitelist and!in_array($tag, $tags)) continue;
                    $len = mb_strlen($tag) + 1;
                    $line = mb_substr($line, $len);
                    $line = trim($line);
                    if (!isset($result[$tag])) $result[$tag] = [];
                    $result[$tag][] = $line;
                }
            }
        }
        return $result;
    }

    /**
     * Parse a Class
     * @param ReflectionClass $reflector
     * @param bool $classParents adds parents to results
     * @return AnnotationInterface[]
     */
    public function parseClass(ReflectionClass $reflector, bool $classParents = false): array {


        $collection = [];

        $className = $reflector->getName();
        if ($classParents) {
            $reflectors = $this->getClassParents($reflector);
        } else $reflectors = [$reflector];
        $others = array_merge($reflector->getProperties(), $reflector->getMethods());
        if (!$classParents) $others = array_filter($others, fn($r) => $r->class === $className);
        $reflectors = array_merge($reflectors, $others);
        foreach ($reflectors as $r) {
            $collection = array_merge($collection, $this->singleDocCommentParser($r));
        }
        return $this->process($collection);
    }

    /**
     * Parse a class using its class name
     * @param string $className
     * @param bool $classParents adds parents to results
     * @return AnnotationInterface[]
     * @throws InvalidArgumentException
     */
    public function parseClassName(string $className, bool $classParents = false): array {
        if (!class_exists($className)and!interface_exists($className)) {
            throw new InvalidArgumentException(sprintf('Invalid Class %s', $className));
        }
        return $this->parseClass(new ReflectionClass($className), $classParents);
    }

    /**
     * Parse a class method
     * @param ReflectionMethod $reflector
     * @return AnnotationInterface[]
     */
    public function parseMethod(ReflectionMethod $reflector): array {
        return $this->process($this->singleDocCommentParser($reflector));
    }

    /**
     * Parse a class Property
     * @param ReflectionProperty $reflector
     * @return AnnotationInterface[]
     */
    public function parseProperty(ReflectionProperty $reflector): array {
        return $this->process($this->singleDocCommentParser($reflector));
    }

    /**
     * Parse Using Reflector
     * @param ReflectionClass|ReflectionProperty|ReflectionMethod $reflector Must implements getDocComment method
     * @return AnnotationInterface[]
     */
    private function singleDocCommentParser($reflector): array {
        $collection = [];
        if (method_exists($reflector, 'getDocComment')) {
            if (($docComment = $reflector->getDocComment()) !== false) {
                $parsed = $this->parseAnnotation($docComment);
                if (count($parsed) > 0) {
                    /** @var string[] $array */
                    foreach ($parsed as $tag => $array) {
                        foreach ($array as $value) {
                            $annotation = $this->annotationFactory->createAnnotation($reflector, $tag, $value);
                            $collection[] = $annotation;
                        }
                    }
                }
            }
        }

        return $collection;
    }

    /**
     * Get Extended Classes
     * @param ReflectionClass $reflector
     * @return array
     * @suppress PhanPossiblyInfiniteLoop
     */
    public function getClassParents(\ReflectionClass $reflector): array {
        $result = [];
        try {
            do {
                $result[] = $reflector;
            } while (($reflector = $reflector->getParentClass()) !== false);
        } catch (ReflectionException $error) { $error->getCode(); }
        return $result;
    }

    /**
     * Process Annotations
     * @param AnnotationInterface[] $annotations
     * @return AnnotationInterface[]
     */
    private function process(array $annotations): array {
        $result = [];
        foreach ($annotations as $annotation) {
            $result[] = $this->processorDispatcher->handle($annotation);
        }
        return $result;
    }

}
