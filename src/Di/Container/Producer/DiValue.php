<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 21.03.18
 * Time: 10:49
 */

namespace Phore\Di\Container\Producer;


use Phore\Di\Container\DiContainer;

class DiValue implements DiResolvable
{

    public function __construct(
        private mixed $value
    ){}

    public function resolve (DiContainer $container, array $optParams = [], \ReflectionClass $class = null, bool $isArray = false)
    {
        return $this->value;
    }

}