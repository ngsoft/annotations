<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface TagHandlerInterface {

    /**
     * Handles Next Filter
     * @param TagInterface $annotation
     * @return TagInterface
     */
    public function handle(TagInterface $annotation): TagInterface;
}
