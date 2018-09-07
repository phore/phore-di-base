<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 07.09.18
 * Time: 13:51
 */

namespace Phore\Di;


use Phore\Di\Container\DiContainer;

interface Resolvable
{
    /**
     * This method is called every time the interface is put
     *
     * @param DiContainer|null $diContainer
     * @return mixed
     */
    public function resolve(DiContainer $diContainer = null, array $params = null);
}