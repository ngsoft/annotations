<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use InvalidArgumentException;
use NGSOFT\{
    Annotations\PhpParser\PhpParser, Interfaces\AnnotationInterface
};
use ReflectionClass;
use function mb_strlen,
             mb_strtolower;

/**
 * Resolves a Class Name
 */
final class ClassNameResolver {

    /**
     * PHP Doc Reserved Words that will be resolved as is
     */
    const RESERVED_KEYWORDS = [
        //gettype
        'boolean', 'integer', 'double', 'string', 'array', 'object', 'resource', 'NULL',
        //aliases
        'bool', 'int', 'float', 'void', 'iterable', 'null', 'mixed', 'static', 'self'
    ];

    /**
     * Caches PhpParser results
     * @var array
     */
    protected static $useCache = [];

    /** @var PhpParser */
    protected $parser;

    public function __construct() {
        $this->parser = new PhpParser();
    }

    /**
     * Resolves $toresolve using annotation
     * @param AnnotationInterface $annotation
     * @param string $toresolve
     * @return string|null
     */
    public function resolve(AnnotationInterface $annotation, string $toresolve): ?string {

        $baseClass = $annotation->getClassName();
        $reflector = $annotation->getReflector() instanceof ReflectionClass ?
                $annotation->getReflector() :
                new ReflectionClass($baseClass);
        return $this->resolveClassName($reflector, $toresolve);
    }

    /**
     * Process $toresolve using Use Statements from $reflector
     * @param ReflectionClass $reflector
     * @param string $toresolve
     * @return string|null
     * @throws InvalidArgumentException
     */
    public function resolveClassName(ReflectionClass $reflector, string $toresolve): ?string {

        if (
                in_array($toresolve, self::RESERVED_KEYWORDS) // case sensitive
                or in_array(mb_strtolower($toresolve), self::RESERVED_KEYWORDS) // case insensitive
                or $this->classExists($toresolve) // class that can be resolved
        ) return $toresolve;

        //resolves class in the same namespace
        $output = sprintf('%s\\%s', $reflector->getNamespaceName(), $toresolve);
        if ($this->classExists($output)) return $output;

        if (!isset(self::$useCache[$reflector->name])) {
            self::$useCache[$reflector->getName()] = $this->parser->parseClass($reflector);
        }


        foreach (self::$useCache[$reflector->getName()] as $lowercase => $statement) {

            //statement ends with $toresolve
            if (
                    substr_compare($statement, $toresolve, -mb_strlen($toresolve)) === 0
                    and $this->classExists($statement)
            ) return $statement;

            if (
                    $lowercase == mb_strtolower($toresolve)
                    and $this->classExists($statement)
            ) return $statement;

            // $statement is a namespace
            $output = sprintf('%s\\%s', $statement, $toresolve);
            if ($this->classExists($output)) return $output;
        }
        return null; //cannot be resolved
    }

    /**
     * Checks if class exists (interface or class)
     * @param string $className
     * @return bool
     */
    public function classExists(string $className): bool {
        return class_exists($className)
                or interface_exists($className);
    }

}
