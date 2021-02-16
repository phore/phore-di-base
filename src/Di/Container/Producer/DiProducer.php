<?php


namespace Phore\Di\Container\Producer;


use Phore\Di\Container\DiContainer;

class DiProducer implements DiResolvable
{

    private $producer;

    public function __construct(callable $producer)
    {
        $this->producer = $producer;
    }


    public function resolve(DiContainer $container, array $optParams = [], \ReflectionClass $class = null, bool $isArray = false)
    {
        return ($this->producer)($container, $optParams, $class, $isArray);
    }
}