<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use RuntimeException;
use function mb_internal_encoding;

mb_internal_encoding("UTF-8");

/**
 * Tests if at least one element in the array passes the test implemented by the provided function..
 * @param callable $callback A function to test for each element
 * @param array $array
 * @return bool
 */
function array_some(callable $callback, array $array): bool {
    foreach ($array as $key => $value) {
        $result = call_user_func_array($callback, [$value, $key, $array]);
        if (!is_bool($result)) throw new RuntimeException('Callback callable must return a boolean.');
        if ($result === true) return true;
    }
    return false;
}

/**
 * Tests if all elements in the array pass the test implemented by the provided function.
 * @param callable $callback A function to test for each element
 * @param array $array
 * @return bool
 */
function array_every(callable $callback, array $array): bool {
    foreach ($array as $key => $value) {
        $result = call_user_func_array($callback, [$value, $key, $array]);
        if (!is_bool($result)) throw new RuntimeException('Callback callable must return a boolean.');
        if ($result === false) return false;
    }
    return true;
}
