<?php


namespace Test;


use Phore\Di\Builder\PhoreCallbackParameterDef;
use Phore\Di\Container\DiContainer;
use Phore\Di\Container\Producer\DiService;
use PHPUnit\Framework\TestCase;

/**
 * Class IntegrationTest
 * @package Test
 * @internal
 */
class IntegrationTest extends TestCase
{

    public function testServiceCall ()
    {
        $c = new DiContainer();
        $c->define("a", new DiService(function () {
            return "A";
        }));
        $c->define("b", new DiService(function () {
            return "B";
        }));

        $i = phore_di_instantiate(\ClassWithConstructor::class, $c);
        $this->assertInstanceOf(\ClassWithConstructor::class, $i);

        $result = phore_di_call(function ($a, $b) { return $a . $b; }, $c);
        $this->assertEquals("AB", $result);
    }

}
