<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Filters;

use NGSOFT\Interfaces\{
    AnnotationFilterInterface, AnnotationInterface
};

class NullFilter implements AnnotationFilterInterface {

    /**
     * {@inheritdoc}
     * @phan-suppress PhanUnusedPublicMethodParameter
     */
    public function filter(AnnotationInterface $annotation): bool {
        return true;
    }

}
