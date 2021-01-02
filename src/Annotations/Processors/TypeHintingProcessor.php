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
     * Resolve Arguments (string $arg1, ?array $arg2)
     * @param AnnotationInterface $annotation
     * @param string $args
     * @return array<string,string|string[]|null>|null
     */
    public function resolveArguments(AnnotationInterface $annotation, string $args): ?array {

        $result = [];
        $args = trim($args);
        if (empty($args)) return $result;
        $args = preg_split('/\h*,\h*/', $args);
        foreach ($args as $input) {

            $input = trim($input);
            //check if hint and var
            if (preg_match('/^\??(?:(\S+)\h+)?\$(\w+)/', $input, $matches)) {
                list(, $hint, $name) = $matches;
                if (empty($hint)) {
                    $result[$name] = null;
                    continue;
                }
                //handle hint
                if ($types = $this->resolveHint($annotation, $hint)) {
                    if (count($types) == 1) $types = $types[0];
                    $result[$name] = $types;
                } else return null;
            }
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
                    $tag = $tag->withAttribute($method)->withValue($types);
                    //handle args
                    if (($arguments = $this->resolveArguments($annotation, $args)) !== null) {
                        return $tag->withParams($arguments);
                    } elseif (!$this->getIgnoreErrors()) throw new AnnotationException($annotation);
                } elseif (!$this->getIgnoreErrors()) throw new AnnotationException($annotation);
                return $tag->withValue(null);
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
