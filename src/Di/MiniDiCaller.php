<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 01.12.16
     * Time: 16:58
     */

    namespace Phore\Di;


    class MiniDiCaller implements DiCaller
    {

        public function __invoke(callable $fn, array $params = [])
        {

        }
    }