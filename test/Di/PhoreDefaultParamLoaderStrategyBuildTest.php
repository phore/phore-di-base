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
        $callable = function() {};
          
        $result = $this->sut->buildParameters($callable);
        
        $this->assertEquals([],$result);
    }


    public function testFailsWithOneParameterWithoutValue()
    {
        $callable = function($param) {};

        $this->expectException(DiUnresolvableException::class);

        $this->sut->buildParameters($callable);
    }


    public function testBuildWithParametersFromOptionalAndDI()
    {
        
        $callable = function($param1, $param2) {};
        $this->diContainer->define("param1", "DI");
        
        $params = $this->sut->buildParameters($callable, ["param2" => "OPT"]);
        
        $this->assertEquals(["DI", "OPT"], $params);
    }


    public function testBuildParametersForStaticMethodOneParamsFromDi()
    {
        $callable = function($param) {};
        $this->diContainer->define("param", "DI");
        
        $result = $this->sut->buildParameters($callable);
        
        $this->assertEquals(["DI"],$result);
    }

    public function testOptionalParametersOverrideDiValues()
    {
        $callable = function($param) {};
        $this->diContainer->define("param", "DI");
        
        $result = $this->sut->buildParameters($callable, ["param" => "OPT"]);
        
        $this->assertEquals(["OPT"],$result);
    }

    public function testDefaultValueOverridesNull()
    {
        $callable = function($param="DEFAULT") {};
        
        $result = $this->sut->buildParameters($callable);
        
        $this->assertEquals(["DEFAULT"],$result);
    }

    public function testDefaultValueOnNull()
    {
        $callable = function($param=null) {};
        
        $result = $this->sut->buildParameters($callable);
        
        $this->assertEquals([null],$result);
    }



    public function testBuildDiOverridesDefaultValue()
    {
        $callable = function($param="default") {};
        $this->diContainer->define("param", "DI");
        
        $result = $this->sut->buildParameters($callable, []);
        
        $this->assertEquals(["DI"],$result);
    }
}

