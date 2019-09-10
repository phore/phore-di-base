<?php


/**
 * Class Demo
 */
class Demo {

    public function __construct(Demo $a1 = null)
    {

    }

    public function fn(int $i)
    {

    }

}


$ref = new ReflectionClass(Demo::class);

echo $ref->getDocComment();

print_r ($ref->getMethods());

foreach ($ref->getMethods() as $method) {
    print_r($method->getParameters());
    foreach ($method->getParameters() as $param) {
        print_r($param);
        echo "is optional" . $param->isOptional();
    }

}
