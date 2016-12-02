<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 02.12.16
     * Time: 11:33
     */

    namespace Phore\Di\Builder;


    interface PhoreParameterBuilderCallback
    {
        public function buildValue (string $paramName, string $paramType, string $paramClassName, mixed $paramDefault, bool $paramAllowsNull, bool $paramIsOptional, bool $paramIsArray, int $paramIndex) : mixed;
    }