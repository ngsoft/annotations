<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface CallableProcessor extends CallableResolverInterface {

    public function resolveProcessor(): callable;
}
