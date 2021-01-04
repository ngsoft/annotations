<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use NGSOFT\Interfaces\{
    AnnotationInterface, TagHandlerInterface, TagInterface
};

class NullHandler implements TagHandlerInterface {

    /** @var bool */
    protected $silentMode = false;

    public function handle(AnnotationInterface $annotation): TagInterface {
        return $annotation->getTag();
    }

    public function setSilentMode(bool $silentMode): TagHandlerInterface {
        $this->silentMode = $silentMode;
        return $this;
    }

}
