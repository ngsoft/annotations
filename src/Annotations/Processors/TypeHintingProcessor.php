<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Processors;

use NGSOFT\{
    Annotations\Tags\TagProperty, Annotations\Utils\ClassNameResolver, Annotations\Utils\ProcessorTrait,
    Exceptions\AnnotationException, Interfaces\AnnotationInterface, Interfaces\TagHandlerInterface, Interfaces\TagInterface,
    Interfaces\TagProcessorInterface
};
use function mb_strpos,
             mb_substr;

/**
 * Type Hint \NGSOFT\Annotations\Tags\TagProperty
 */
class TypeHintingProcessor implements TagProcessorInterface {

    use ProcessorTrait;

    /** @var ClassNameResolver */
    protected $resolver;

    public function __construct() {
        $this->resolver = new ClassNameResolver();
    }

    /**
     * {@inheritdoc}
     * @suppress PhanUnusedVariable
     */
    public function process(AnnotationInterface $annotation, TagHandlerInterface $handler): TagInterface {

        $tag = $annotation->getTag();

        if (
                !$this->isIgnored($tag)
                and $tag instanceof TagProperty
        ) {

            $input = $tag->getValue();

            // @param value1|value2 [$varname] ignored
            if (preg_match('/^(\S+)(?:\h+\$(\w+))?\h*/', $input, $matches) > 0) {
                $result = [];

                $name = '';
                if (count($matches) > 2) list(, $types, $name) = $matches;
                else list(, $types) = $matches;

                $types = explode('|', $types);

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
                    if ($resolved = $this->resolver->resolve($annotation, $toresolve)) {
                        $result[] = $resolved . $suffix;
                    } elseif ($this->ignoreErrors) continue;
                    else throw new AnnotationException($annotation);
                }

                if (count($result) == 1) $result = $result[0];

                return $tag->withAttributeName($name)->withValue($result);
            }
        }

        return $handler->handle($annotation); // pass to next processor
    }

}
