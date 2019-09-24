<?php

namespace Di;

use InvalidArgumentException;
use Phore\Di\Builder\PhoreDefaultParamLoaderStrategy;
use Phore\Di\Container\DiContainer;
use PHPUnit\Framework\TestCase;

class PhoreDefaultParamLoaderStrategyTest extends TestCase
{
    public $sut;
    public $diContainer;

    protected function setUp(): void
    {
        $this->diContainer = new DiContainer();
        $this->sut = new PhoreDefaultParamLoaderStrategy($this->diContainer);
    }

    public function testBuildParametersForStaticMethod()
    {
        //Arrange
        $callable = [SomeClass::class, "staticMethodNoParams"];
        //Act  
        $result = $this->sut->buildParameters($callable);
        //Assert
        $this->assertEquals([],$result);
    }

    public function testBuildParametersForStaticMethodOneParamsWithParamArray()
    {
        //Arrange
        $callable = [SomeClass::class, "staticMethodOneParams"];
        //Act
        $result = $this->sut->buildParameters($callable, ["param" => true]);
        //Assert
        $this->assertEquals([true],$result);
    }

    public function testBuildParametersThrowsExceptionIfParamHasNoValue()
    {
        //Arrange
        $callable = [SomeClass::class, "staticMethodOneParams"];

        //Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("[object:ReflectionParameter]");

        //Act
        $this->sut->buildParameters($callable);
    }

    public function testBuildParametersForStaticMethodOneParamsFromDi()
    {
        //Arrange
        $callable = [SomeClass::class, "staticMethodOneParams"];
        $this->diContainer->define("param", true);
        //Act
        $result = $this->sut->buildParameters($callable);
        //Assert
        $this->assertEquals([true],$result);
    }

    public function testBuildParametersForStaticMethodWithDefaultValueParam()
    {
        //Arrange
        $callable = [SomeClass::class, "staticMethodOptionalParams"];
        //Act
        $result = $this->sut->buildParameters($callable);
        //Assert
        $this->assertEquals([true],$result);
    }

    public function testBuildParametersForStaticMethodWithDefaultValueParamNull()
    {
        //Arrange
        $callable = [SomeClass::class, "staticMethodOptionalParamsNull"];
        //Act
        $result = $this->sut->buildParameters($callable);
        //Assert
        $this->assertEquals([null],$result);
    }

    public function testBuildParametersForStaticMethodPriorizeParamValue()
    {
        //Arrange
        $callable = [SomeClass::class, "staticMethodOptionalParams"];
        $this->diContainer->define("param", "test");
        //Act
        $result = $this->sut->buildParameters($callable, ["param" => false]);
        //Assert
        $this->assertEquals([false],$result);
    }

    public function testBuildParametersForStaticMixedMethod()
    {
        //Arrange
        $callable = [SomeClass::class, "staticMethodMixedParams"];
        $this->diContainer->define("param", "test");
        //Act
        $result = $this->sut->buildParameters($callable, ["opt2" => "foo"]);
        //Assert
        $this->assertEquals(["test",true,"foo"],$result);
    }

    public function testBuildParametersForMixedMethod()
    {
        //Arrange
        //TODO: FIND OUT HOW THIS WORKS!!!
        $callable = [SomeClass::class, "methodMixedParams"];
        $this->diContainer->define("param", "test");
        //Act
        $result = $this->sut->buildParameters($callable, ["opt2" => "foo"]);
        //Assert
        $this->assertEquals(["test",true,"foo"],$result);
    }

    public function testBuildParametersForMixedMethodWithObject()
    {
        //Arrange
        $testObject = new SomeClass("const");
        $callable = [$testObject, "methodMixedParams"];
        $this->diContainer->define("param", "test");
        //Act
        $result = $this->sut->buildParameters($callable, ["opt2" => "foo"]);
        //Assert
        $this->assertEquals(["test",true,"foo"],$result);
    }

    public function testBuildParametersForLambdaFunction()
    {
        //Arrange
        $callable = function ($param, $opt1=true, $opt2 = false)
        {
            return [$param, $opt1, $opt2];
        };
        $this->diContainer->define("param", "test");
        //Act
        $result = $this->sut->buildParameters($callable, ["opt2" => "foo"]);
        //Assert
        $this->assertEquals(["test",true,"foo"],$result);
    }

    public function testBuildParametersForConstructor()
    {
        //Arrange
        $callable = [SomeClass::class, "__construct"];
        $this->diContainer->define("param", "test");
        //Act
        $result = $this->sut->buildParameters($callable);
        //Assert
        $this->assertEquals(["test","string"],$result);
    }

}

class SomeClass {

    public function __construct($param, $opt1="string")
    {
    }

    public static function staticMethodNoParams()
    {
        return true;
    }

    public static function staticMethodOneParams($param)
    {
        return $param;
    }

    public static function staticMethodMultipleParams($param1, $param2)
    {
        return [$param1, $param2];
    }

    public static function staticMethodOptionalParams($param=true)
    {
        return $param;
    }

    public static function staticMethodOptionalParamsNull($param=null)
    {
        return $param;
    }

    public static function staticMethodMixedParams($param, $opt1=true, $opt2 = false)
    {
        return [$param, $opt1, $opt2];
    }

    public function methodMixedParams($param, $opt1=true, $opt2 = false)
    {
        return [$param, $opt1, $opt2];
    }

}
