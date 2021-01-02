<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use InvalidArgumentException;
use NGSOFT\{
    Annotations\Utils\AnnotationFactory, Annotations\Utils\Cache, Annotations\Utils\Dispatcher, Interfaces\AnnotationFactoryInterface,
    Interfaces\AnnotationInterface
};
use Psr\Cache\{
    CacheItemInterface, CacheItemPoolInterface
};
use ReflectionClass,
    ReflectionException,
    ReflectionMethod,
    ReflectionProperty,
    SplFileInfo;
use function mb_internal_encoding,
             mb_strlen,
             mb_strpos,
             mb_substr;

mb_internal_encoding("UTF-8");

/**
 * Parses Class for annotations
 */
class AnnotationParser {

    /**
     * Version Information
     */
    const VERSION = '1.0';

    /**
     * Tag Matching Regex
     */
    const TAG_MATCH_REGEX = '/^@(\w[\w\-\\\]+)\h?+/';

    /**
     * Tags That are always ignored
     */
    const DEFAULT_IGNORE_TAGS = [
        'inheritdoc', 'phan'
    ];

    /** @var AnnotationFactoryInterface */
    protected $annotationFactory;

    /** @var Dispatcher */
    protected $processorDispatcher;

    /** @var string[] */
    protected $ignoreTags = [];

    /** @var CacheItemPoolInterface */
    protected $cache;

    public function __construct(
            ?Dispatcher $processorDispatcher = null,
            ?AnnotationFactoryInterface $annotationFactory = null
    ) {
        $this->ignoreTags = self::DEFAULT_IGNORE_TAGS;
        if ($annotationFactory instanceof AnnotationFactoryInterface) $this->annotationFactory = $annotationFactory;
        $this->annotationFactory = new AnnotationFactory();
        if ($processorDispatcher instanceof Dispatcher) $this->processorDispatcher = $processorDispatcher;
        else $this->processorDispatcher = new Dispatcher();
    }

    /**
     * Set Silent Mode Value For the processors
     * @param bool $silentMode
     * @return static
     */
    public function setSilentMode(bool $silentMode): self {
        $this->processorDispatcher->setSilentMode($silentMode);
        return $this;
    }

    /**
     * Blacklist one to many tags
     * @param string ...$tags
     * @return self
     */
    public function addIgnoredTags(string ...$tags): self {
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
                if (preg_match(self::TAG_MATCH_REGEX, $line, $matches) > 0) {
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
     * @param string[] $tags Tags to search for (empty is unlimited)
     * @return AnnotationInterface[]
     * @suppress PhanTypeMismatchArgumentNullable
     */
    public function parseClass(ReflectionClass $reflector, bool $classParents = false, array $tags = []): array {
        $cacheEnabled = false;
        if ($item = $this->getCacheItem($reflector, $classParents, $tags)) {
            if ($item->isHit()) return $item->get();
            $cacheEnabled = true;
        }

        $collection = [];

        $className = $reflector->getName();
        if ($classParents) {
            $reflectors = $this->getClassParents($reflector);
        } else $reflectors = [$reflector];
        $others = array_merge($reflector->getProperties(), $reflector->getMethods());
        if (!$classParents) $others = array_filter($others, fn($r) => $r->class === $className);
        $reflectors = array_merge($reflectors, $others);
        foreach ($reflectors as $r) {
            $collection = array_merge($collection, $this->singleDocCommentParser($r, $tags));
        }
        $result = $this->process($collection);

        if ($cacheEnabled) {
            $item->set($result);
            $this->saveCacheItem($item);
        }
        return $result;
    }

    /**
     * Parse a class using its class name
     * @param string $className
     * @param bool $classParents adds parents to results
     * @param string[] $tags Tags to search for (empty is unlimited)
     * @return AnnotationInterface[]
     * @throws InvalidArgumentException
     */
    public function parseClassName(string $className, bool $classParents = false, array $tags = []): array {
        if (!class_exists($className)and!interface_exists($className)) {
            throw new InvalidArgumentException(sprintf('Invalid Class %s', $className));
        }
        return $this->parseClass(new ReflectionClass($className), $classParents, $tags);
    }

    /**
     * Parse a class method
     * @param ReflectionMethod $reflector
     * @param string[] $tags Tags to search for (empty is unlimited)
     * @return AnnotationInterface[]
     */
    public function parseMethod(ReflectionMethod $reflector, array $tags = []): array {
        return $this->process($this->singleDocCommentParser($reflector, $tags));
    }

    /**
     * Parse a class Property
     * @param ReflectionProperty $reflector
     * @param string[] $tags Tags to search for (empty is unlimited)
     * @return AnnotationInterface[]
     */
    public function parseProperty(ReflectionProperty $reflector, array $tags = []): array {
        return $this->process($this->singleDocCommentParser($reflector, $tags));
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
     * Set Cache Pool
     * @param CacheItemPoolInterface $cache
     * @param int|null $ttl
     * @return self
     */
    public function setCachePool(CacheItemPoolInterface $cache, ?int $ttl = null): self {
        $this->cache = new Cache($cache, $ttl);
        return $this;
    }

    /**
     * Get parsed annotations cached version
     * @param ReflectionClass $reflector
     * @param bool $classParents
     * @param string[] $tags Tags to search for
     * @return CacheItemInterface|null
     */
    protected function getCacheItem(ReflectionClass $reflector, bool $classParents, array $tags): ?CacheItemInterface {
        if (!$this->cache instanceof CacheItemPoolInterface) return null;

        $key = 'annotations' . implode('', $tags);
        $classes = [$reflector];
        if ($classParents) $classes = $this->getClassParents($reflector);

        foreach ($classes as $classReflector) {
            $filename = $classReflector->getFileName();
            $fileinfo = new SplFileInfo($filename);
            $key .= $fileinfo->getMTime() . $fileinfo->getPathname();
        }
        $key = md5($key);
        return $this->cache->getItem($key);
    }

    /**
     * Save to cache
     * @param CacheItemInterface $item
     * @return bool
     */
    protected function saveCacheItem(CacheItemInterface $item): bool {
        return $this->cache->save($item);
    }

    /**
     * Parse Using Reflector
     * @param ReflectionClass|ReflectionProperty|ReflectionMethod $reflector Must implements getDocComment method
     * @param string[] $tags Tags to search for
     * @return AnnotationInterface[]
     */
    protected function singleDocCommentParser($reflector, array $tags = []): array {
        $collection = [];
        if (method_exists($reflector, 'getDocComment')) {
            if (($docComment = $reflector->getDocComment()) !== false) {
                $parsed = $this->parseAnnotation($docComment, $tags);
                if (count($parsed) > 0) {
                    /** @var string[] $array */
                    foreach ($parsed as $tagName => $array) {
                        foreach ($array as $value) {
                            $tag = $this->annotationFactory->createTag($tagName, $value);
                            $annotation = $this->annotationFactory->createAnnotation($reflector, $tag);
                            $collection[] = $annotation;
                        }
                    }
                }
            }
        }

        return $collection;
    }

    /**
     * Process Annotations
     * @param AnnotationInterface[] $annotations
     * @return AnnotationInterface[]
     */
    protected function process(array $annotations): array {
        $result = [];
        foreach ($annotations as $annotation) {
            $result[] = $this->processorDispatcher->handle($annotation);
        }
        return $result;
    }

}
