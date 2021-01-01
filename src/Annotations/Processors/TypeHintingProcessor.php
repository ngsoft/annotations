<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Processors;

use InvalidArgumentException;
use NGSOFT\{
    Annotations\PhpParser\PhpParser, Annotations\Tags\TagProperty, Exceptions\AnnotationException, Interfaces\AnnotationInterface,
    Interfaces\TagHandlerInterface, Interfaces\TagInterface, Interfaces\TagProcessorInterface
};
use ReflectionClass;
use function mb_strpos,
             mb_substr;

/**
 * Type Hint \NGSOFT\Annotations\Tags\TagProperty
 */
class TypeHintingProcessor implements TagProcessorInterface {

    const RESERVED_KEYWORDS = [
        //gettype
        'boolean', 'integer', 'double', 'string', 'array', 'object', 'resource', 'NULL',
        //aliases
        'bool', 'int', 'float', 'void', 'iterable', 'null', 'mixed', 'static', 'self'
    ];

    /** @var array */
    protected static $useCache = [];

    /**
     * Process $toresolve using Use Statements from $baseClass
     * @param string $baseClass $annotation->getClassName()
     * @param string $toresolve
     * @return string|null null cannot be resolved
     */
    public function resolveClassName(string $baseClass, string $toresolve): ?string {

        if (!class_exists($baseClass) and!interface_exists($baseClass)) {
            throw new InvalidArgumentException(sprintf('Invalid base class "%s".', $baseClass));
        }
        if (
                in_array($toresolve, self::RESERVED_KEYWORDS)
                or $toresolve[0] == '\\'
        ) return $toresolve;

        if (class_exists($toresolve) or interface_exists($toresolve)) return $toresolve;

        $reflector = new ReflectionClass($baseClass);

        //resolves class in the same namespace
        $output = sprintf('%s\\%s', $reflector->getNamespaceName(), $toresolve);
        if ($this->exists($output)) return $output;


        if (!isset(self::$useCache[$baseClass])) {
            $parser = new PhpParser();
            self::$useCache[$baseClass] = $parser->parseClass($reflector);
        }



        foreach (self::$useCache[$baseClass] as $lowercase => $statement) {

            //statement ends with $toresolve
            if (
                    substr_compare($statement, $toresolve, -mb_strlen($toresolve)) === 0
                    and $this->exists($statement)
            ) return $statement;

            if (
                    $lowercase == mb_strtolower($toresolve)
                    and $this->exists($statement)
            ) return $statement;

            // $statement is a namespace
            $output = sprintf('%s\\%s', $statement, $toresolve);
            if ($this->exists($output)) return $output;
        }




        return null;
    }

    /**
     * Checks if class exists
     * @param string $className
     * @return bool
     */
    protected function exists(string $className): bool {
        return class_exists($className) or interface_exists($className);
    }

    public function process(AnnotationInterface $annotation, TagHandlerInterface $handler): TagInterface {

        $tag = $annotation->getTag();

        if ($tag instanceof TagProperty) {

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
                    if ($resolved = $this->resolveClassName($annotation->getClassName(), $toresolve)) {
                        $result[] = $resolved . $suffix;
                    } else throw new AnnotationException($annotation);
                }

                if (count($result) == 1) $result = $result[0];

                return $tag->withAttributeName($name)->withValue($result);
            }
        }




        return $handler->handle($annotation);
    }

}
