<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 07.09.18
 * Time: 13:53
 */

namespace Phore\Di;


use Phore\Di\Container\DiContainer;

class Deferred implements Resolvable
{
    private $value;
    private $isResolved = false;
    private $resolveFn;


    public function __construct(callable $resolveFn)
    {
        $this->resolveFn = $resolveFn;
    }

    /**
     * This method is called every time the interface is put
     *
     * @param DiContainer|null $diContainer
     * @return mixed
     */
    public function resolve(DiContainer $diContainer = null, array $params = [])
    {
        if ($this->isResolved) {
            return $this->value;
        }
        $args = [];

        if ($diContainer !== null) {
            $args = $diContainer->buildParametersFor($this->resolveFn, $params);
        }
        $this->value = ($this->resolveFn)(...$args);
        $this->isResolved = true;
        return $this->value;
    }
}