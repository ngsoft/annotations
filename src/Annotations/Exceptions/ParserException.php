<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Exceptions;

use ReflectionClass,
    ReflectionMethod,
    ReflectionProperty,
    Reflector,
    RuntimeException,
    Throwable;

class ParserException extends RuntimeException {

    /** @var ReflectionClass|ReflectionProperty|ReflectionMethod  */
    protected $reflector;

    public function __construct(
            Reflector $reflector,
            string $message = '',
            int $code = 0,
            ?Throwable $previous = null
    ) {
        $this->reflector = $reflector;
        parent::__construct($message, $code, $previous);
    }

    /** @return ReflectionClass|ReflectionProperty|ReflectionMethod */
    public function getReflector() {
        return $this->reflector;
    }

}
