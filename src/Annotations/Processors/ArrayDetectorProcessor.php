<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Processors;

use JsonException;
use NGSOFT\{
    Annotations\Tags\TagList, Annotations\Tags\TagProperty, Exceptions\AnnotationException, Interfaces\AnnotationInterface,
    Interfaces\TagHandlerInterface, Interfaces\TagInterface, Interfaces\TagProcessorInterface
};
use function mb_strpos;

class ArrayDetectorProcessor implements TagProcessorInterface {

    const DETECT_LIST_REGEX = '/^[\(](.*?)[\)]/';
    const DETECT_KEY_VALUE_PAIR = '/^[{](.*?)[}]/';

    /** @var string[] */
    public static $ignoreTagClasses = [
        TagProperty::class,
        TagList::class,
    ];

    /**
     * Detects if list
     * @param string $input
     * @return bool
     */
    private function isList(string $input): bool {
        return mb_strpos($input, '(') === 0 and mb_strpos($input, ')') !== false;
    }

    /**
     * Get Real Value for $input
     * @param string $input
     * @return mixed|null
     */
    private function getRealValue(string $input) {
        $input = trim($input);
        if (mb_strpos($input, '=') !== false) return null;
        try {
            $output = json_decode($input, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $error) {
            $error->getCode();
            if (preg_match('/^\w+$/', $input) > 0) $output = $input;
            else return null;
        }
        return $output;
    }

    /**
     * Parse Key PAir List {a=50, b="value", custom_arg = true}
     * @param string $input
     * @return array
     */
    private function parseKeyPairList(string $input): array {
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
    private function parseList(string $input): array {
        $result = [];
        if (preg_match(self::DETECT_LIST_REGEX, $input, $matches) > 0) {

            list(, $args) = $matches;
            $args = trim($args);
            if (
                    mb_strpos($args, '{') === 0
                    and ($pos = mb_strpos($args, '}')) !== false
                    and ($poseq = mb_strpos($args, '=')) !== false
                    and $pos > $poseq
            ) {
                return $this->parseKeyPairList($args);
            }

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

        if (!in_array(get_class($tag), self::$ignoreTagClasses)) {
            $input = $tag->getValue();
            if ($this->isList($input)) {
                $output = $this->parseList($input);
                if (count($output) > 0) {
                    if (count($output) === 1 and array_key_exists(0, $output)) return $tag->withValue($output[0]);
                    else return new TagList($tag->getName(), $output);
                } else throw new AnnotationException($annotation);
            }
        }
        return $handler->handle($annotation);
    }

}
