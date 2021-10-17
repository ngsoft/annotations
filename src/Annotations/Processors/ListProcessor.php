<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Processors;

use JsonException;
use NGSOFT\{
    Annotations\Tags\TagBoolean, Annotations\Tags\TagList, Annotations\Tags\TagProperty, Annotations\Utils\AnnotationFactory, Annotations\Utils\ClassNameResolver,
    Annotations\Utils\Processor, Exceptions\AnnotationException, Interfaces\AnnotationFactoryInterface, Interfaces\AnnotationInterface, Interfaces\TagHandlerInterface,
    Interfaces\TagInterface, Interfaces\TagProcessorInterface
};
use function mb_strpos;

class ListProcessor extends Processor implements TagProcessorInterface {

    const DETECT_LIST_REGEX = '/^[\(](.*?)[\)]/';
    const DETECT_KEY_VALUE_PAIR = '/^\{(.*?)\}/';

    /** @var AnnotationFactoryInterface */
    protected $annotationFactory;

    /** @var ClassNameResolver */
    protected $classNameResolver;

    /** @param AnnotationFactoryInterface|null $annotationFactory */
    public function __construct(
            ?AnnotationFactoryInterface $annotationFactory = null
    ) {

        $this->annotationFactory = $annotationFactory ?? new AnnotationFactory();
        $this->classNameResolver = new ClassNameResolver();
        $this->addIgnoreTagClass(TagProperty::class);
        $this->addIgnoreTagClass(TagBoolean::class);
    }

    /**
     * Detects if list
     * @param string $input
     * @return bool
     */
    protected function isList(string $input): bool {
        return !empty($input) && $input[0] === '(' and mb_strpos($input, ')') !== false;
    }

    /**
     * Get Real Value for $input
     * @param string $input
     * @return mixed|null
     */
    protected function getRealValue(string $input) {
        $input = trim($input);
        if (mb_strpos($input, '=') !== false) return null;
        try {
            $output = json_decode($input, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $error) {
            $error->getCode();
            //can be a word without quotes
            if (preg_match('/^\w+$/', $input) > 0) $output = $input;
            else return null; //but cannot
        }
        return $output;
    }

    /**
     * Parse Key PAir List {a=50, b="value", custom_arg = true}
     * @param string $input
     * @return array
     */
    protected function parseKeyPairList(string $input): array {

        $result = [];

        if (preg_match(self::DETECT_KEY_VALUE_PAIR, $input, $matches) > 0) {
            list(, $args) = $matches;
            $args = preg_split('/\h*,\h*/', $args);
            foreach ($args as $argInput) {

                if (preg_match('/\h*(\w+)\h*=(.*)/', $argInput, $matchesArg) > 0) {
                    list(, $key, $value) = $matchesArg;
                    $output = $this->getRealValue($value);
                    if ($output === null) continue;
                    $result[$key] = $output;
                }
            }
        }
        return $result;
    }

    /**
     * Capture the list
     * @param string $input
     * @return array
     */
    protected function parseList(string $input): array {
        $result = [];
        if (preg_match(self::DETECT_LIST_REGEX, $input, $matches) > 0) {

            list(, $args) = $matches;
            $args = trim($args);
            if ($args[0] !== '{' and $args[-1] !== '}') $args = sprintf('{%s}', $args);
            // list type (value1= "my value 1", value2= "my 2nd value")
            // or ({value1= "my value 1", value2= "my 2nd value"})

            if (
                    mb_strpos($args, '{') === 0
                    and ($pos = mb_strpos($args, '}')) !== false
                    and ($poseq = mb_strpos($args, '=')) !== false
                    and $pos > $poseq
            ) {
                return $this->parseKeyPairList($args);
            }

            // kist type (value1, value2)
            $args = trim($args, '{}');
            $args = preg_split('/\h*,\h*/', $args);
            foreach ($args as $value) {
                $output = $this->getRealValue($value);
                if ($output === null) continue;
                $result[] = $output;
            }
        }

        return $result;
    }

    /** {@inheritdoc} */
    public function process(AnnotationInterface $annotation, TagHandlerInterface $handler): TagInterface {
        $tag = $annotation->getTag();

        /** @var string $tagclass */
        $tagClass = TagList::class;

        if ($tag instanceof TagList) {
            if (is_array($tag->getValue())) return $tag;
            $tagClass = get_class($tag); //TagList or extended
        }


        if (
                !$this->isIgnored($tag)
                and is_string($tag->getValue())
        ) {
            $input = $tag->getValue();

            if ($this->isList($input)) {

                $output = $this->parseList($input);

                //resolve class name
                foreach ($output as &$value) {
                    if (
                            is_string($value)
                            and $className = $this->classNameResolver->resolve($annotation, $value)
                    ) {
                        $value = $className;
                    }
                }
                if ($tag instanceof TagList) return $tag->withValue($output);
                return (new $tagClass)
                                ->withName($tag->getName())
                                ->withValue($output);
            }
            //can be a custom class extending TagList so returns a resolved result
            if ($tag instanceof TagList) {
                //resolve class name
                if (
                        is_string($tag->getValue())
                        and $className = $this->classNameResolver->resolve($annotation, $tag->getValue())
                ) {

                    return $tag->withValue($className);
                }
                // resolve others
                $tagToResolve = $this->annotationFactory->createTag($tag->getName(), $input); //creates basic tag so it isn't ignored by the other processors
                $resolvedTag = $handler->handle($annotation->withTag($tagToResolve)); // use other processors

                if ($input === $resolvedTag->getValue()) {
                    //no change -> handle error
                    if (!$this->getSilentMode()) throw new AnnotationException($annotation);
                    else return $tag->withValue(null);
                } else return $tag->withValue($resolvedTag->getValue());
            }
        }
        return $handler->handle($annotation);
    }

}
