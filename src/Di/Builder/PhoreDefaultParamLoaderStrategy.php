<?php


namespace Phore\Di\Builder;


use Phore\Di\Container\DiContainer;
use Phore\Di\Container\DiUnresolvableException;
use Phore\Di\Container\DiUnresolvableInternalException;
use ReflectionParameter;

class PhoreDefaultParamLoaderStrategy
{

    /**
     * @var DiContainer
     */
    private $diContainer;

    /**
     * PhoreDefaultParamLoaderStrategy constructor.
     * @param DiContainer $diContainer
     */
    public function __construct(DiContainer $diContainer)
    {
        $this->diContainer = $diContainer;
    }

    public function buildParameters($callable, array $optParameters = []): array
    {
        $paramValues = [];
        $reflectionParams = phore_func_params($callable);

        foreach ($reflectionParams as $paramIndex => $reflectionParam) {
            try {
                $value = $this->_getParamValue($reflectionParam, $optParameters);
                $this->_validate($reflectionParam, $value);
                $paramValues[] = $value;
            } catch (DiUnresolvableInternalException $ex) {
                // Don't catch DiResolvableExceptions occuring down the stack.
                throw new DiUnresolvableException("Cannot build Parameter " . ($paramIndex + 1) . " ({$reflectionParam->getName()}) for " . phore_var($callable) . ": " . $ex->getMessage());
            }
        }
        return $paramValues;
    }


    /**
     * @param ReflectionParameter $param
     * @param $value
     * @throws DiUnresolvableInternalException
     */
    private function _validate(ReflectionParameter $param, $value)
    {

        if ($param->isArray()) {
            if ($value === null && $param->isOptional()) {
                return;
            } // No error if optional
            if (!is_array($value)) {
                throw new DiUnresolvableInternalException("Incompatible parameter type 'array': " . phore_var($value) . " found");
            }
            return;
        }

        if ($param->getClass() !== null) {
            if ($value === null && $param->isOptional()) {
                return;
            } // No error if optional
            if (!is_object($value)) {
                throw new DiUnresolvableInternalException("Expected object of class '{$param->getClass()->getName()}': " . phore_var($value) . " found");
            }
            if (!is_a(  $value, $param->getClass()->getName())) {
                throw new DiUnresolvableInternalException("Incompatible class: Expected subclass of '{$param->getClass()->getName()}'" . phore_var($value) . " found");
            }
            return;
        }

        if ($param->getType() !== null) {
            if ($param->getType()->allowsNull() && $value === null) {
                return;
            }

            switch ($param->getType()->getName()) {
                case "int":
                    if (!is_int($value)) {
                        throw new DiUnresolvableInternalException("Incompatible type: Expected {$param->getType()->getName()}: " . phore_var($value) . " found");
                    }
                    return;

                case "float":
                    if (!is_double($value)) {
                        throw new DiUnresolvableInternalException("Incompatible type: Expected {$param->getType()->getName()}: " . phore_var($value) . " found");
                    }
                    return;

                case "bool":
                    if (!is_bool($value)) {
                        throw new DiUnresolvableInternalException("Incompatible type: Expected {$param->getType()->getName()}: " . phore_var($value) . " found");
                    }
                    return;

                case "string":
                    if (!is_string($value)) {
                        throw new DiUnresolvableInternalException("Incompatible type: Expected {$param->getType()->getName()}: " . phore_var($value) . " found");
                    }
                    return;

                case "callable":
                    if (!is_callable($value)) {
                        throw new DiUnresolvableInternalException("Incompatible type: Expected {$param->getType()->getName()}: " . phore_var($value) . " found");
                    }
                    return;

                case "object":
                    if (!is_object($value)) {
                        throw new DiUnresolvableInternalException("Incompatible type: Expected {$param->getType()->getName()}: " . phore_var($value) . " found");
                    }
                    return;

                default:
                    throw new DiUnresolvableInternalException("Unrecoginzed type: {$param->getType()->getName()} - Check");
            }
        }

    }

    private function _getParamValue(ReflectionParameter $reflectionParam, array $optParameters)
    {
        $name = $reflectionParam->getName();

        if (array_key_exists($name, $optParameters)) {
            return $optParameters[$name];
        }

        if ($this->diContainer->has($name)) {
            return $this->diContainer->get($name);
        }

        if ($reflectionParam->isOptional()) {
            return $reflectionParam->getDefaultValue();
        }

        throw new DiUnresolvableInternalException("Unknown key '$name'");
    }
}