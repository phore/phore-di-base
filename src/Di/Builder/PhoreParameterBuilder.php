<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 02.12.16
     * Time: 11:31
     */

    namespace Phore\Di;


    use Phore\Di\Builder\PhoreCallbackParameterDef;
    use Phore\Di\Builder\PhoreParameterBuilderCallback;

    class PhoreParameterBuilder
    {

        /**
         * @var PhoreParameterBuilderCallback
         */
        private $mBuilder;

        public function __construct (PhoreParameterBuilderCallback $builder) {
            $this->mBuilder = $builder;
        }



        public function buildParamDef (callable $callable) : PhoreCallbackParameterDef {
            $paramDef = new PhoreCallbackParameterDef();

            if (is_array($callable)) {
                if (is_object($callable[0])) {
                    $ref = new \ReflectionMethod(get_class($callable[0]), $callable[1]);
                } else if (is_string($callable[0])) {
                    $ref = new \ReflectionMethod($callable[0], $callable[1]);
                } else {
                    throw new \InvalidArgumentException("Array is no valid callback." . var_export($callable, true));
                }
            } else {
                $ref = new \ReflectionFunction($callable);
            }

            $parameterRef = $ref->getParameters();

            foreach ($parameterRef as $curParam) {
                $className = null;
                if ($curParam->getClass() !== null) {
                    $className = $curParam->getClass()->getName();
                }

                $paramDef->parameters[] = [
                    $curParam->getName(),
                    $curParam->getType(),
                    $className,
                    $curParam->getDefaultValue(),
                    $curParam->allowsNull(),
                    $curParam->isOptional(),
                    $curParam->isArray()
                ];
            }
            return $paramDef;
        }

        public function buildParams (PhoreCallbackParameterDef $paramDef)
        {
            $params = [];
            foreach ($paramDef->parameters as $curParams) {
                $params[] = $this->mBuilder->buildValue(...$curParams);
            }
            return $params;

        }

    }