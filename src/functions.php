<?php


function phore_func_params(): array
{

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
        default:
            $var = 'Unexpected value';
    }

    return "[$type:$var]";
}
