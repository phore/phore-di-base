<?php


namespace Phore\Di\Helper;


use Phore\Di\Container\DiContainer;
use Phore\Di\Container\DiUnresolvableException;
use Phore\Di\Container\DiUnresolvableInternalException;
use Phore\Di\Container\Producer\DiResolvable;

class PhoreParameterHelper
{


    /**
     * Return a Array of ReflectionParameter Objects
     *
     * @param $callable
     * @return \ReflectionParameter[]
     */
    public function getReflectionParameters($callable)
    {
        if (is_array($callable)) {
            if (is_object($callable[0])) {
                 if (is_callable($callable)) {
                    $ref = new \ReflectionMethod(get_class($callable[0]), $callable[1]);
                    return $ref->getParameters();
                } else {
                    throw new \InvalidArgumentException("Array is not callable.");
                }
            } else if (is_string($callable[0])) {
                if ($callable[1] === "__construct") {
                    $ref = new \ReflectionClass($callable[0]);
                    if (($constructorRef = $ref->getConstructor()) === null) {
                        return [];
                    } else {
                        return $ref->getConstructor()->getParameters();
                    }
                }

                $ref = new \ReflectionMethod($callable[0], $callable[1]);
                return $ref->getParameters();
            } else {
                throw new \InvalidArgumentException("Array is no valid callback.");
            }
        } else {
            $ref = new \ReflectionFunction($callable);
            return $ref->getParameters();
        }
    }


    /**
     * @param \ReflectionParameter[] $reflectionParameters
     * @param DiContainer $diContainer
     * @param array $optParams
     * @return array
     */
    public function buildParameters (array $reflectionParameters, DiContainer $diContainer, array $optParams = []) : array
    {
        $parameters = [];
        foreach ($reflectionParameters as $reflectionParameter) {
            $name = $reflectionParameter->getName();

            // Load from optParams
            if (isset ($optParams[$name])) {
                $optParamVal = $optParams[$name];
                if ($optParamVal instanceof DiResolvable) {
                    $parameters[] = $optParamVal->resolve($diContainer, $optParams);
                    continue;
                }
                $parameters[] = $optParamVal;
                continue;
            }

            if ($diContainer->has($name)) {
                $val = $diContainer->resolve($name, $optParams);
                $parameters[] = $val;
                continue;
            }

            if ( ! $reflectionParameter->isOptional()) {
                throw new DiUnresolvableInternalException("Parameter '$name' cannot be resolved.");
            }
            $default = $reflectionParameter->getDefaultValue();
            $parameters[] = $default;
            continue;
        }
        return $parameters;
    }


}