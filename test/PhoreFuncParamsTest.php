<?php


namespace Test;


use Phore\Di\Builder\PhoreCallbackParameterDef;
use PHPUnit\Framework\TestCase;

class PhoreFuncParamsTest extends TestCase
{

    /**
     * @throws \ReflectionException
     */
    public function testLambdaFunction()
    {
        $l = function($a) {};
        $params = phore_func_params($l);

        $this->assertEquals(1, count($params));
    }

    public function testClassWithConstructor()
    {
        $object = new ClassWithConstructor(1);
        $params = phore_func_params([$object, "__construct"]);

        $this->assertEquals(1, count($params));
    }

    public function testClassWithoutConstructor()
    {
        $object = new ClassWithoutConstructor();
        $params = phore_func_params([$object, "__construct"]);

        $this->assertEquals(0, count($params));
    }

    public function testStaticMethod()
    {
        $params = phore_func_params([ClassWithConstructor::class, "staticMethod"]);

        $this->assertEquals(1, count($params));
    }

    public function testMethod()
    {
        $object = new ClassWithConstructor(1);
        $params = phore_func_params([$object, "method"]);

        $this->assertEquals(1, count($params));
    }

    public function testExceptionMethodNotInObject()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Array is not callable: Method does not exist.");

        $object = new ClassWithConstructor(1);
        $params = phore_func_params([$object, "noMethod"]);
    }

    public function testExceptionArrayHasNoObjectOrClass()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Array is not callable.");

        $object = 1;
        $params = phore_func_params([$object, "noMethod"]);
    }

    public function testExceptionArrayIsEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Array is not callable.");

        $params = phore_func_params([]);
    }

    public function testExceptionParameterIsNull()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Array is not callable.");

        $params = phore_func_params(null);
    }



}

/**
 * Class ClassWithConstructor
 * @package Test
 * @internal
 */
class ClassWithConstructor
{
    public function __construct($a)
    {

    }

    public function method($p)
    {

    }

    public static function staticMethod($p)
    {

    }
}

/**
 * Class ClassWithoutConstructor
 * @package Test
 * @internal
 */
class ClassWithoutConstructor
{

}