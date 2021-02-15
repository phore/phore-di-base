<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 21.03.18
 * Time: 10:50
 */

namespace Phore\Di\Container\Producer;


use Phore\Di\Container\DiContainer;

class DiService implements DiResolvable
{

    private $value;
    private $isResolved = false;

    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }


    public function resolve(DiContainer $container, array $optParams = [], \ReflectionClass $class = null, bool $isArray = false)
    {
        if( ! $this->isResolved) {
            $this->isResolved = true;
            $this->value = phore_di_call($this->factory, $container, $optParams);
        }
        return $this->value;
    }

}