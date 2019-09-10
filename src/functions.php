<?php

use Phore\Di\Builder\PhoreCallbackParameterDef;

/**
 * @param callable $callable
 * @return ReflectionParameter[]
 */
function phore_func_params($callable) : array {
    if (is_array($callable)) {
        if (is_object($callable[0])) {
            if($callable[1] === "__construct") {
                $ref = new \ReflectionClass(get_class($callable[0]));
                if (($constructorRef = $ref->getConstructor()) === null) {
                    return [];
                } else {
                    return $ref->getConstructor()->getParameters();
                }
            } elseif (is_callable($callable)) {
                $ref = new \ReflectionMethod(get_class($callable[0]), $callable[1]);
                return $ref->getParameters();
            } else {
                throw new \InvalidArgumentException("Array is not callable.");
            }
        } else if (is_string($callable[0])) {
            $ref = new \ReflectionMethod($callable[0], $callable[1]);
            return $ref->getParameters();
        } else {
            throw new \InvalidArgumentException("Array is no valid callback.");
        }
    } else {
        $ref = new \ReflectionFunction($callable);
        return $ref->getParameters();
    }
}
