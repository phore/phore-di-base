<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 21.03.18
 * Time: 10:40
 */

namespace Phore\Di\Container;


use Phore\Di\Builder\PhoreParameterBuilderCallback;
use Phore\Di\Container\Producer\DiResolvable;
use Phore\Di\Container\Producer\DiService;
use Phore\Di\Container\Producer\DiValue;
use Phore\Di\Caller\PhoreBaseDiCaller;
use Psr\Container\ContainerInterface;

class DiContainer implements ContainerInterface
{

    /**
     * @var DiResolvable[]
     */
    private $instances = [];

    public function __construct()
    {

    }


    public function has($name) : bool
    {
        return $this->isResolvable($name);
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws DiUnresolvableException
     * @throws \ErrorException
     */
    public function get($name)
    {
        return $this->resolve($name);
    }


    public function isResolvable($name, array $addParams = []) : bool
    {
        if (isset ($addParams[$name])) {
            return true;
        } else if (isset ($this->instances[$name])) {
            return true;
        }
        return false;
    }


    /**
     * @param       $name
     * @param array $addParams
     *
     * @return
     * @throws DiUnresolvableException
     * @throws \ErrorException
     */
    public function resolve($name, array $addParams = [], \ReflectionClass $class = null, bool $isArray = false)
    {
        if (isset ($addParams[$name])) {
            $resv = $addParams[$name];
            $resv = $this->argumentToResolvable($resv);
        } else if (isset ($this->instances[$name])) {
            $resv = $this->instances[$name];
        } else {
            throw new DiUnresolvableException("'$name' is not resolvable by di-container.");
        }
        if (!$resv instanceof DiResolvable) {
            throw new \ErrorException(
                "Expected DiResolvable. Found: ".gettype($resv)
                ." for name '$name'"
            );
        }
        return $resv->resolve($this, $addParams, $class, $isArray);
    }


    /**
     * Wrap allowed arguments (callbacks, etc) into DiResolvable Objects
     *
     * @param $input
     *
     * @return DiResolvable
     */
    public function argumentToResolvable ($input) : DiResolvable
    {
        if ( ! $input instanceof DiResolvable) {
            if (is_callable($input) && ! is_string($input)) {
                return new DiService($input);
            } else {
                return new DiValue($input);
            }
        }
        return $input;
    }

    /**
     * Add a new Value/Factory to Dependency Injection
     *
     * <Example>
     * $di->add("param1", "New Value");
     * $di->add("param1", function () { echo "new value"; });
     * $di->add("param1", new DiService());
     * </Example>
     *
     * @deprecated Use define() instead
     *
     * @param $name
     * @param $factoryOrValue
     *
     *
     * @return DiContainer
     */
    public function add ($name, $factoryOrValue) : self
    {
        return $this->define($name, $factoryOrValue);
    }

    /**
     * Add a new Value/Factory to Dependency Injection
     *
     * <Example>
     * $di->define("param1", new DiValue("New Value"));
     * $di->define("param1", new DiService (function () { echo "new value"; }));
     * </Example>
     *
     * @param $name
     * @param $factoryOrValue
     *
     * @return DiContainer
     */
    public function define ($name, DiResolvable $factoryOrValue) : self
    {
        $factoryOrValue = $this->argumentToResolvable($factoryOrValue);
        $this->instances[$name] = $factoryOrValue;
        return $this;
    }



    public function __get($name)
    {
        return $this->resolve($name);
    }

    public function __set($name, $val)
    {
        throw new \InvalidArgumentException("Trying to set '$name' on app. Use define() to inject something.");
    }

    public function __isset ($name)
    {
        return $this->has($name);
    }
}