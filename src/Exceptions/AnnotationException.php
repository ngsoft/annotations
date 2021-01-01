<?php

declare(strict_types=1);

namespace NGSOFT\Exceptions;

use NGSOFT\Interfaces\AnnotationInterface,
    RuntimeException,
    Throwable;

class AnnotationException extends RuntimeException {

    /** @var AnnotationInterface */
    protected $annotation;

    /**
     * @param AnnotationInterface $annotation
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
            AnnotationInterface $annotation,
            string $message = '',
            int $code = 0,
            ?Throwable $previous = null
    ) {
        $this->annotation = $annotation;

        if (empty($message)) {

            $message = sprintf(
                    'Cannot parse %s Annotation "@%s %s" in file "%s"',
                    $annotation->getType(),
                    $annotation->getTag(),
                    $annotation->getValue(),
                    $annotation->getFileName()
            );
        }



        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the Annotation for which Exception has been thown
     * @return AnnotationInterface
     */
    public function getAnnotation(): AnnotationInterface {
        return $this->annotation;
    }

}
