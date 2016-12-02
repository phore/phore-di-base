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

    class PhoreParameterBuilderTest extends \PHPUnit_Framework_TestCase
    {


        public function testParameterBuilder () {
            $c = new PhoreParameterBuilder();

            $fn = function (array $param1, int $param2, $param3=null) {

            };

            // Build the definition (cachable)
            $def = $c->buildParamDef($fn);

            // Load the Parameters
            $params = $c->buildParams($def, new class implements PhoreParameterBuilderCallback {

                public function buildValue(
                        string $paramName,
                        string $paramType,
                        string $paramClassName,
                        mixed $paramDefault,
                        bool $paramAllowsNull,
                        bool $paramIsOptional,
                        bool $paramIsArray,
                        int $paramIndex
                ) : mixed
                {
                    return "VALUE";
                }
            });

            // Call the Function with unpacking the parameters array
            $fn(...$params);

        }

    }
