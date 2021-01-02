<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Processors;

use NGSOFT\{
    Annotations\Tags\TagProperty, Annotations\Utils\ClassNameResolver, Annotations\Utils\Processor, Exceptions\AnnotationException,
    Interfaces\AnnotationInterface, Interfaces\TagHandlerInterface, Interfaces\TagInterface, Interfaces\TagProcessorInterface
};
use function mb_strpos,
             mb_substr;

/**
 * Type Hint \NGSOFT\Annotations\Tags\TagProperty
 */
class TypeHintingProcessor extends Processor implements TagProcessorInterface {

    /** @var ClassNameResolver */
    protected $classNameResolver;

    /** @var ListProcessor */
    protected $listProcessor;

    public function __construct() {
        $this->classNameResolver = new ClassNameResolver();
        $this->listProcessor = new ListProcessor();
    }

    /**
     * Resolve Hint (?ClassName|callable|null)
     * @param AnnotationInterface $annotation
     * @param string $hint
     * @return string[]|null
     * @throws AnnotationException
     * @suppress PhanUnusedVariable
     */
    public function resolveHint(AnnotationInterface $annotation, string $hint): ?array {

        $types = explode('|', $hint);
        $result = [];

        foreach ($types as $type) {
            $type = $toresolve = trim($type);
            $suffix = '';
            if (
                    ($pos = mb_strpos($type, '<')) !== false
                    or ($pos = mb_strpos($type, '[')) !== false
            ) {
                $toresolve = mb_substr($type, 0, $pos);
                $suffix = mb_substr($type, $pos);
            }
            if ($resolved = $this->classNameResolver->resolve($annotation, $toresolve)) {
                $result[] = $resolved . $suffix;
            } else return null;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     * @suppress PhanUnusedVariable
     */
    public function process(AnnotationInterface $annotation, TagHandlerInterface $handler): TagInterface {

        $tag = $annotation->getTag();

        if (
                !$this->isIgnored($tag)
                and is_string($tag->getValue())
                and $tag instanceof TagProperty
        ) {

            $input = $tag->getValue();

            // @method values|value2 functionName(?attr $varname)
            if (preg_match('/^\??(\S+)\h+(\w+)\h*\((.*)\)/', $input, $matches) > 0) {

                list(, $hint, $method, $args) = $matches;

                //handle hint
                if ($types = $this->resolveHint($annotation, $hint)) {
                    $tag = $tag->withValue($types);
                    //handle method
                } elseif (!$this->getIgnoreErrors()) throw new AnnotationException($annotation);
                else return $tag->withValue(null);






                var_dump($matches);

















                return $handler->handle($annotation);
            }

            // @param value1|value2 [$varname] ignored
            elseif (preg_match('/^\??(\S+)(?:\h+\$(\w+))?\h*/', $input, $matches) > 0) {
                $name = '';
                if (count($matches) > 2) list(, $hint, $name) = $matches;
                else list(, $hint) = $matches;

                if ($result = $this->resolveHint($annotation, $hint)) {
                    if (count($result) == 1) $result = $result[0];
                    return $tag->withAttribute($name)->withValue($result);
                } elseif (!$this->getIgnoreErrors()) throw new AnnotationException($annotation);
                //empty array or null
                return $tag->withValue(null);
            }
        }

        return $handler->handle($annotation); // pass to next processor
    }

}
