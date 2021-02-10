<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 02.12.16
     * Time: 16:58
     */

    namespace Phore\Test\Di;


    use Phore\Di\Builder\PhoreParameterBuilderCallback;
    use Phore\Di\Builder\PhoreParameterBuilder;
    use Phore\Di\Helper\PhoreParameterHelper;
    use PHPUnit\Framework\TestCase;
    use Test\ClassWithConstructor;

    /**
     * Class PhoreParameterHelperTest
     * @package Phore\Test\Di
     * @internal
     */
    class PhoreParameterHelperTest extends TestCase
    {


        public function setUp() : void {
            require_once __DIR__ . "/../../test/Mock/mockobjects.inc.php";
        }


        public function testBuildParamsWithClass () {
            $h = new PhoreParameterHelper();

            $params = $h->getReflectionParameters([\ClassWithConstructor::class, "__construct"]);
            $this->assertInstanceOf(\ReflectionParameter::class, $params[0]);
            $this->assertInstanceOf(\ReflectionParameter::class, $params[1]);
            $this->assertCount(2, $params);
        }

        public function testBuildParamsWithClassWithoutConstructor () {
            $h = new PhoreParameterHelper();

            $params = $h->getReflectionParameters([\ClassWithOutConstructor::class, "__construct"]);
            $this->assertCount(0, $params);
        }

        public function testBuildParamsWithMethod () {
            $h = new PhoreParameterHelper();

            $params = $h->getReflectionParameters([\ClassWithOutConstructor::class, "publicMethod"]);
            $this->assertCount(2, $params);
        }

        public function testBuildParamsWithLambda () {
            $h = new PhoreParameterHelper();

            $params = $h->getReflectionParameters(function ($a, $b) {});
            $this->assertCount(2, $params);
        }



        public function testBuildParameters()
        {

        }



    }
