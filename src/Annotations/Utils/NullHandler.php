<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use NGSOFT\Interfaces\{
    AnnotationInterface, TagHandlerInterface, TagInterface
};

class NullHandler implements TagHandlerInterface {

    public function handle(AnnotationInterface $annotation): TagInterface {
        return $annotation->getTag();
    }

}
