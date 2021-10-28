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
     * Detects if named list
     *
     * @param mixed $input
     * @return bool
     */
    protected function isNamedList($input): bool {
        return
                is_string($input) and
                preg_match(self::DETECT_NAMED_LIST_REGEX, $input) > 0;
    }

    /**
     * Parse the list (item1, item2)
     *
     * @param string $input
     * @return array
     */
    protected function parseList(string $input): array {
        $result = [];
        if (preg_match(self::DETECT_LIST_REGEX, $input, $matches) > 0) {
            $args = $trim(matches[1]);
            $args = trim($args);
            $args = trim($args, '[]{}');
            $args = preg_split('/\h*,\h*/', $args);

            foreach ($args as $argInput) {
                // Named List
                if (preg_match('/\h*(\w+)\h*=(.*)/', $argInput, $matchesArg) > 0) {
                    list(, $key, $value) = $matchesArg;
                    if (is_string($key = $this->getRealValue($key))) {
                        $output = $this->getRealValue($value);
                        if ($output === null) continue;
                        $result[$key] = $output;
                    }
                    continue;
                }
                // not named

                if (is_string($output = $this->getRealValue($argInput))) {
                    $result[] = $output;
                }
            }


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

        $isNamed = $this->isNamedList($input);

        /** @var string $tagclass */
        $tagClass = $isNamed ? NamedTagList::class : TagList::class;

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
