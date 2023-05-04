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
        } else if (is_object($callable)) {
            $ref = new \ReflectionObject($callable);
            return $ref->getMethod("__invoke")->getParameters();
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
    public function buildParameters (array $reflectionParameters, DiContainer $diContainer, array $optParams = [], &$failedParam=null) : array
    {
        $parameters = [];
        foreach ($reflectionParameters as $reflectionParameter) {
            $name = $reflectionParameter->getName();
            $failedParam = $name;
            // Load from optParams
            if (isset ($optParams[$name])) {
                $optParamVal = $optParams[$name];
                if ($optParamVal instanceof DiResolvable) {
                    $parameterClass = $reflectionParameter->getType() && !$reflectionParameter->getType()->isBuiltin()
                        ? new \ReflectionClass($reflectionParameter->getType()->getName()) : null;
                    $parameterArray =   $reflectionParameter->getType() && $reflectionParameter->getType()->getName() === 'array';
                    $parameters[] = $optParamVal->resolve($diContainer, $optParams, $parameterClass, $parameterArray);
                    continue;
                }
                $parameters[] = $optParamVal;
                continue;
            }

            if ($diContainer->has($name)) {
                $parameterClass = $reflectionParameter->getType() && !$reflectionParameter->getType()->isBuiltin()
                    ? new \ReflectionClass($reflectionParameter->getType()->getName()) : null;
                $parameterArray =   $reflectionParameter->getType() && $reflectionParameter->getType()->getName() === 'array';
                $val = $diContainer->resolve($name, $optParams, $parameterClass, $parameterArray);
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
        $failedParam = null;
        return $parameters;
    }


}
