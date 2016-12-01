<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 01.12.16
     * Time: 16:58
     */

    namespace Phore\Di;


    /**
     * Class PhoreBaseDiCaller
     *
     * This Class implents only the __invoke() functionality
     * to inject parameters in a callable.
     *
     * @package Phore\Di
     */
    class PhoreBaseDiCaller implements DiCaller
    {

        public function __invoke(callable $fn, array $params = [])
        {

        }
    }