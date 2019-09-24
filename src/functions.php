<?php

/**
 * get reflection parameters for any callback
 *
 * <example>
 *  phore_func_params([SomeClass::class, "__construct"]) // constructor
 *  phore_func_params([SomeClass::class, "SomeStaticMethod"]); //static method
 *  phore_func_params([$object, "SomeMethod"]) //method
 *  phore_func_params(function($a, $b, $c){}) //lambda function
 * </example>
 *
 * @param mixed $callable
 * @return ReflectionParameter[]
 * @throws ReflectionException
 */
function phore_func_params($callable): array
{
    if (is_array($callable) && count($callable) === 2) {
        if (is_object($callable[0])) {
            if ($callable[1] === "__construct") {
                $ref = new \ReflectionClass(get_class($callable[0]));
                if (($constructorRef = $ref->getConstructor()) === null) {
                    return [];
                } else {
                    return $ref->getConstructor()->getParameters();
                }
            } elseif (is_callable($callable)) {
                $ref = new \ReflectionMethod(get_class($callable[0]), $callable[1]);
                return $ref->getParameters();
            } else {
                throw new \InvalidArgumentException("Array is not callable: Method does not exist.");
            }
        } else {
            if (is_string($callable[0])) {
                $ref = new \ReflectionMethod($callable[0], $callable[1]);
                return $ref->getParameters();
            } else {
                throw new \InvalidArgumentException("Array is not callable.");
            }
        }
    } elseif (is_callable($callable)) {
        $ref = new \ReflectionFunction($callable);
        return $ref->getParameters();
    } else {
        throw new \InvalidArgumentException("Array is not callable.");
    }
}

function phore_var($var): string
{
    $type = gettype($var);

    switch ($type) {
        case "boolean" :
            $var = $var ? "true" : "false";
            break;
        case "array" :
            $var = count($var);
            break;
        case "object" :
            $var = get_class($var);
            break;
        case "resource" :
            $var = get_resource_type($var);
            break;
        case "double":
        case "integer":
        case "NULL":
        case "string":
            break;
        default:
            $var = 'Unexpected value';
    }
    return "[$type:$var]";
}
