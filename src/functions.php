<?php

use Phore\Di\Builder\PhoreCallbackParameterDef;


/**
 * Return new instance of an class specified in parameter 1
 *
 * Di is applied to the classes constructor
 *
 * @param string $className
 * @param \Phore\Di\Container\DiContainer $container
 * @param array $params
 * @return object
 * @throws \Phore\Di\Container\DiUnresolvableInternalException
 */
function phore_di_instantiate(string $className, \Phore\Di\Container\DiContainer $container, array $params = [])
{
    $helper = new \Phore\Di\Helper\PhoreParameterHelper();
    $reflectionParameters = $helper->getReflectionParameters([$className, "__construct"]);

    $parameters = $helper->buildParameters($reflectionParameters, $container, $params);



    return new $className(...$parameters);
}

/**
 * Call the callable in parameter 1 using dependeny injection and return the results
 *
 * @param callable $callable
 * @param \Phore\Di\Container\DiContainer $container
 * @param array $params
 * @return mixed
 * @throws \Phore\Di\Container\DiUnresolvableInternalException
 */
function phore_di_call(callable $callable, \Phore\Di\Container\DiContainer $container, array $params = [])
{
    $helper = new \Phore\Di\Helper\PhoreParameterHelper();
    $reflectionParameters = $helper->getReflectionParameters($callable);

    try {
        $parameters = $helper->buildParameters($reflectionParameters, $container, $params);
    } catch (Exception $e) {
        $ref = new ReflectionFunction($callable);
        throw new InvalidArgumentException("Exception with Message: '$e->getMessage()' occured while building parameters for " .
            $ref->getFileName() .
            " [Line:" . $ref->getStartLine() . "-" . $ref->getEndLine() . "]", $e->getCode(), $e);
    } catch (Error $e) {
        $ref = new ReflectionFunction($callable);
        throw new Error("Error with Message: '$e->getMessage()' occured while building parameters for " .
            $ref->getFileName() .
            " [Line:" . $ref->getStartLine() . "-" . $ref->getEndLine() . "]", $e->getCode(), $e);
    }


    return $callable(...$parameters);
}


function phore_debug_type($var): string
{
    $type = get_debug_type($var);

    switch ($type) {
        case "bool" :
            $var = $var ? "true" : "false";
            break;
        case "array" :
            $var = count($var);
            break;
        case "Closure" :
            $ref = new ReflectionFunction($var);
            $var = $ref->getFileName() . " Line:" . $ref->getStartLine() . "-" . $ref->getEndLine();
            break;
        case "object" :
            $var = get_class($var);
            break;
        case "resource" :
            $var = get_resource_type($var);
            break;
        case "integer" :
        case "string" :
        case "float" :
            break;
        case "null" :
            $var = "";
            break;
        default:
            $var = 'Unexpected value:' . $type;
    }

    return "[$type:$var]";
}
