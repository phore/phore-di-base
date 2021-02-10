<?php

/**
 * @internal
 */
class ClassWithConstructor  {
    public function __construct ($a, $b) {}
}

/**
 * @internal
 */
class ClassWithOutConstructor {

    public function publicMethod($a, $b) {}

    public static function staticMethod ($a, $b) {}
}
