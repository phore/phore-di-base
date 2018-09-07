<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 07.09.18
 * Time: 13:47
 */

function phore_deferred(callable $resolveFn) : \Phore\Di\Container\Producer\DiResolvable
{
    return new \Phore\Di\Container\Producer\DiService($resolveFn);
}

function phore_resolve($input, \Phore\Di\Container\DiContainer $diContainer = null)
{
    if ($input instanceof \Phore\Di\Container\Producer\DiResolvable)
        return $input->resolve($diContainer);
    return $input;
}