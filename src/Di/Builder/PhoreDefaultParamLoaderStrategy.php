<?php


namespace Phore\Di\Builder;


use Phore\Di\Container\DiContainer;

class PhoreDefaultParamLoaderStrategy
{
    private $diContainer;

    /**
     * PhoreDefaultParamLoaderStrategy constructor.
     * @param DiContainer $diContainer
     */
    public function __construct(DiContainer $diContainer)
    {
        $this->diContainer = $diContainer;
    }

    public function buildParameters($callable, array $optParameters = []) : array
    {
        $paramValues = [];
        $reflectionParams = phore_func_params($callable);

        foreach ($reflectionParams as $reflectionParam) {
            $paramValues[] = $this->getParamValue($reflectionParam, $optParameters);
        }
        return $paramValues;
    }

    private function getParamValue($reflectionParam, $optParameters) {
        $name = $reflectionParam->getName();

        if (array_key_exists($name, $optParameters)) {
            return  $optParameters[$name];
        }

        if ($this->diContainer->has($name)){
            return $this->diContainer->get($name);
        }

        if ($reflectionParam->isOptional()){
            return $reflectionParam->getDefaultValue();
        }

        throw new \InvalidArgumentException(phore_var($reflectionParam));
    }
}