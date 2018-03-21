<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 01.12.16
     * Time: 16:58
     */

    namespace Phore\Di;

    use Phore\Di\Builder\PhoreParameterBuilder;
    use Phore\Di\Builder\PhoreParameterBuilderCallback;
    use Phore\Di\Container\DiUnresolvableException;
    use Phore\Di\Container\DiUnresolvableInternalException;


    /**
     * Class PhoreBaseDiCaller
     *
     * This Class implents only the __invoke() functionality
     * to inject parameters in a callable.
     *
     * @package Phore\Di
     */
    class PhoreBaseDiCaller implements DiCaller
    {
        /**
         * @var PhoreParameterBuilder
         */
        private $mParamBuilder;

        private $mBuilder;

        public function __construct()
        {
            $this->mParamBuilder = new PhoreParameterBuilder();
            $this->mBuilder = new class implements PhoreParameterBuilderCallback {


                public function buildValue(
                        array $values,
                        string $paramName,
                        string $paramType = null,
                        string $paramClassName = null,
                        $paramDefault,
                        bool $paramAllowsNull,
                        bool $paramIsOptional,
                        bool $paramIsArray,
                        int $paramIndex
                ) {
                    return $values[$paramName];
                }
            };
        }


        protected function __setBuilder (PhoreParameterBuilderCallback $builder)
        {
            $this->mBuilder = $builder;
        }


        /**
         * @param callable $fn
         * @param array    $params
         *
         * @return mixed
         * @throws DiUnresolvableException
         */
        public function __invoke(callable $fn, array $params = [])
        {
            try {
                $def = $this->mParamBuilder->buildParamDef($fn);
                $this->mBuilder->curParams = $params;
                $funcParams = $this->mParamBuilder->buildParams(
                    $def,
                    $this->mBuilder,
                    $params
                );

                return $fn(...$funcParams);
            } catch (DiUnresolvableInternalException $ex) {
                throw new DiUnresolvableException("Unresolvable __invoke: " . (string)$fn . ": " . $ex->getMessage());
            }
        }
    }