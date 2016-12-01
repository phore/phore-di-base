<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 01.12.16
     * Time: 16:54
     */

    namespace Phore\Di;


    interface DiCaller
    {

        public function __invoke (callable $fn, array $params = []);
    }