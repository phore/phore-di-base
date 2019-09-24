<?php

namespace Di;

use InvalidArgumentException;
use Phore\Di\Builder\PhoreDefaultParamLoaderStrategy;
use Phore\Di\Container\DiContainer;
use Phore\Di\Container\DiUnresolvableException;
use PHPUnit\Framework\TestCase;


/**
 * Class PhoreDefaultParamLoaderStrategyTest
 * @package Di
 * @internal
 */
class PhoreDefaultParamLoaderStrategyTest extends TestCase
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

    public function testBuildsWithNotParameters()
    {
        //Arrange
        $callable = function() {};
        //Act  
        $result = $this->sut->buildParameters($callable);
        //Assert
        $this->assertEquals([],$result);
    }


    public function testFailsWithOneParameterWithoutValue()
    {
        //Arrange
        $callable = function($param) {};

        //Assert
        $this->expectException(DiUnresolvableException::class);

        //Act
        $this->sut->buildParameters($callable);
    }


    public function testBuildWithParametersFromOptionalAndDI()
    {
        //Arrange
        $callable = function($param1, $param2) {};
        $this->diContainer->define("param1", "DI");

        //Act
        $params = $this->sut->buildParameters($callable, ["param2" => "OPT"]);

        //Assert
        $this->assertEquals(["DI", "OPT"], $params);
    }


    public function testBuildParametersForStaticMethodOneParamsFromDi()
    {
        //Arrange
        $callable = function($param) {};
        $this->diContainer->define("param", "DI");
        //Act
        $result = $this->sut->buildParameters($callable);
        //Assert
        $this->assertEquals(["DI"],$result);
    }

    public function testOptionalParametersOverrideDiValues()
    {
        //Arrange
        $callable = function($param) {};
        $this->diContainer->define("param", "DI");
        //Act
        $result = $this->sut->buildParameters($callable, ["param" => "OPT"]);
        //Assert
        $this->assertEquals(["OPT"],$result);
    }

    public function testDefaultValueOverridesNull()
    {
        //Arrange
        $callable = function($param="DEFAULT") {};
        //Act
        $result = $this->sut->buildParameters($callable);
        //Assert
        $this->assertEquals(["DEFAULT"],$result);
    }

    public function testDefaultValueOnNull()
    {
        //Arrange
        $callable = function($param=null) {};
        //Act
        $result = $this->sut->buildParameters($callable);
        //Assert
        $this->assertEquals([null],$result);
    }



    public function testBuildDiOverridesDefaultValue()
    {
        //Arrange
        $callable = function($param="default") {};
        $this->diContainer->define("param", "DI");
        //Act
        $result = $this->sut->buildParameters($callable, []);
        //Assert
        $this->assertEquals(["DI"],$result);
    }
}

