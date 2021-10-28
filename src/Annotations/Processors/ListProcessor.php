<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Processors;

use JsonException;
use NGSOFT\{
    Annotations\Tags\NamedTagList, Annotations\Tags\TagBoolean, Annotations\Tags\TagList, Annotations\Tags\TagProperty, Annotations\Utils\AnnotationFactory,
    Annotations\Utils\ClassNameResolver, Annotations\Utils\Processor, Exceptions\AnnotationException, Interfaces\AnnotationInterface, Interfaces\TagHandlerInterface,
    Interfaces\TagInterface, Interfaces\TagProcessorInterface
};
use function mb_strpos;

class ListProcessor extends Processor implements TagProcessorInterface {

    const DETECT_LIST_REGEX = '/^[\(](.*?)[\)]/';
    const DETECT_NAMED_LIST_REGEX = '/\w+\h*=\h*/';
    const DETECT_KEY_VALUE_PAIR = '/^\{(.*?)\}/';

    /** @var AnnotationFactory */
    protected $annotationFactory;

    /** @var ClassNameResolver */
    protected $classNameResolver;

    /** @param AnnotationFactory|null $annotationFactory */
    public function __construct(
            ?AnnotationFactory $annotationFactory = null
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
        return
                !empty($input) and
                $input[0] === '(' and
                mb_strpos($input, ')') !== false;
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
     * Parse Key Pair List {a=50, b="value", custom_arg = true}
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
     * Detects if named list
     *
     * @param mixed $input
     * @return type
     */
    protected function isNamedList($input) {
        return
                is_string($input) and
                preg_match(self::DETECT_NAMED_LIST_REGEX, $input) > 0;
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
            if (empty($args) or ($args[0] !== '{' and $args[-1] !== '}')) $args = sprintf('{%s}', $args);
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

        if ($this->isIgnored($tag)) return $handler->handle($annotation);
        $input = $tag->getValue();

        /** @var string $tagclass */
        $tagClass = $this->isNamedList($input) ? NamedTagList::class : TagList::class;

        if ($tag instanceof TagList) {
            if ($input) return $tag;
            $tagClass = get_class($tag); //TagList or extended
        }



        if (is_string($input)) {



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

                if (is_string(key($output))) $tagClass = NamedTagList::class;
                return (new $tagClass)
                                ->withName($tag->getName())
                                ->withValue($output);
            }
            //can be a custom class extending TagList so handle error
            if ($tag instanceof TagList) {
                if (!$this->getSilentMode()) throw new AnnotationException($annotation);
                else return $tag->withValue([]);
            }
        }
        return $handler->handle($annotation);
    }

}
