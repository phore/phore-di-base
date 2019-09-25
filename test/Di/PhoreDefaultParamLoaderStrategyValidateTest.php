<?php

namespace Di;

require __DIR__ . "/../mock/TestA.php";
require __DIR__ . "/../mock/TestAB.php";

use Phore\Di\Builder\PhoreDefaultParamLoaderStrategy;
use Phore\Di\Container\DiContainer;
use Phore\Di\Container\DiUnresolvableException;
use Phore\Di\Container\Producer\DiValue;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;
use TestA;
use TestAB;


/**
 * Class PhoreDefaultParamLoaderStrategyTest
 * @package Di
 * @internal
 */
class PhoreDefaultParamLoaderStrategyValidateTest extends TestCase
{
    /**
     * @var PhoreDefaultParamLoaderStrategy
     */
    public $sut;

    /**
     * @var DiContainer
     */
    public $diContainer;

    protected function setUp(): void
    {
        $this->diContainer = new DiContainer();
        $this->sut = new PhoreDefaultParamLoaderStrategy($this->diContainer);
    }

    public function testThrowsExceptionWhenParamTypeDoesNotMatchInt()
    {
        $callable = function (int $param) {
        };
        $this->diContainer->define("param", "DI");

        $this->expectException(DiUnresolvableException::class);
        $this->expectExceptionMessage("Cannot build Parameter 1 (param) for [object:Closure]: Incompatible type: Expected int:");

        $this->sut->buildParameters($callable);
    }

    public function testThrowsExceptionWhenParamTypeDoesNotMatchArray()
    {
        $callable = function (array $param) {
        };
        $this->diContainer->define("param", "DI");

        $this->expectException(DiUnresolvableException::class);
        $this->expectExceptionMessage("Cannot build Parameter 1 (param) for [object:Closure]: Incompatible parameter type 'array':");

        $this->sut->buildParameters($callable);
    }

    public function testThrowsExceptionWhenParamTypeDoesNotMatchClass()
    {
        $callable = function (DiContainer $param) {
        };
        $this->diContainer->define("param", "DI");

        $this->expectException(DiUnresolvableException::class);
        $this->expectExceptionMessage("Cannot build Parameter 1 (param) for [object:Closure]: Expected object of class 'Phore\Di\Container\DiContainer':");

        $this->sut->buildParameters($callable);
    }

    public function testThrowsExceptionWhenParamTypeDoesNotMatchDouble()
    {
        $callable = function (float $param) {
        };
        $this->diContainer->define("param", "DI");

        $this->expectException(DiUnresolvableException::class);
        $this->expectExceptionMessage("Cannot build Parameter 1 (param) for [object:Closure]: Incompatible type: Expected float:");

        $this->sut->buildParameters($callable);
    }

    public function testThrowsExceptionWhenParamTypeDoesNotMatchBool()
    {
        $callable = function (bool $param) {
        };
        $this->diContainer->define("param", 1.23);

        $this->expectException(DiUnresolvableException::class);
        $this->expectExceptionMessage("Cannot build Parameter 1 (param) for [object:Closure]: Incompatible type: Expected bool:");

        $this->sut->buildParameters($callable);
    }

    public function testThrowsExceptionWhenParamTypeDoesNotMatchString()
    {
        $callable = function (string $param) {
        };
        $this->diContainer->define("param", 1.23);

        $this->expectException(DiUnresolvableException::class);
        $this->expectExceptionMessage("Cannot build Parameter 1 (param) for [object:Closure]: Incompatible type: Expected string:");

        $this->sut->buildParameters($callable);
    }

    public function testThrowsExceptionWhenParamTypeDoesNotMatchCallable()
    {
        $callable = function (callable $param) {
        };
        $this->diContainer->define("param", 1.23);

        $this->expectException(DiUnresolvableException::class);
        $this->expectExceptionMessage("Cannot build Parameter 1 (param) for [object:Closure]: Incompatible type: Expected callable:");

        $this->sut->buildParameters($callable);
    }

    public function testThrowsExceptionWhenParamTypeDoesNotMatchSubclass()
    {
        $callable = function (TestAB $param) {
        };
        $parent = new TestA();
        $this->diContainer->define("param", $parent);

        $this->expectException(DiUnresolvableException::class);
        $this->expectExceptionMessage("Cannot build Parameter 1 (param) for [object:Closure]: Incompatible class: Expected subclass of 'TestAB'[object:TestA] found");

        $this->sut->buildParameters($callable);
    }

    public function testThrowsExceptionWhenParamTypeDoesNotMatchObject()
    {
        $callable = function (object $param) {
        };
        $this->diContainer->define("param", 1.23);

        $this->expectException(DiUnresolvableException::class);
        $this->expectExceptionMessage("Cannot build Parameter 1 (param) for [object:Closure]: Incompatible type: Expected object:");

        $this->sut->buildParameters($callable);
    }

    public function testBuildsParametersIfTypesMatch()
    {
        $callable = function (
            int $param1,
            float $param2,
            bool $param3,
            string $param4,
            callable $param5,
            object $param6,
            array $param7,
            TestA $param8
        ) {
        };

        $testCallable = function () {
        };
        $testObj = new Object_();

        $this->diContainer->define("param1", 1);
        $this->diContainer->define("param2", 1.23);
        $this->diContainer->define("param3", true);
        $this->diContainer->define("param4", "DI");
        $this->diContainer->define("param5", new DiValue($testCallable));
        $this->diContainer->define("param6", $testObj);
        $this->diContainer->define("param7", []);
        $this->diContainer->define("param8", new TestA());

        $result = $this->sut->buildParameters($callable);

        $this->assertEquals([1, 1.23, true, "DI", $testCallable, $testObj, [], new TestA()], $result);
    }

    public function testBuildsOptionalParameters()
    {
        $callable = function (
            int $param1 = null,
            float $param2 = null,
            bool $param3 = null,
            string $param4 = null,
            callable $param5 = null,
            object $param6 = null,
            array $param7 = null,
            TestA $param8 = null
        ) {
        };

        $result = $this->sut->buildParameters($callable);

        $this->assertEquals([null, null, null, null, null, null, null, null], $result);
    }
}

