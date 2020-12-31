<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface CallableResolverInterface {

    /**
     * Resolve $toResolve into a callable
     *
     * @param string|callable $toResolve
     * @return callable
     */
    public function resolve($toResolve): callable;
}
