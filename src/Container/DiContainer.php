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

class DiContainer extends PhoreBaseDiCaller implements ContainerInterface
{

    /**
     * @var DiResolvable[]
     */
    private $instances = [];

    public function __construct()
    {
        parent::__construct();
        $this->__setBuilder(new class ($this) implements PhoreParameterBuilderCallback {

            private $diContainer;

            public function __construct(DiContainer $diContainer)
            {
                $this->diContainer = $diContainer;
            }

            /**
             * @param array       $values
             * @param string      $paramName
             * @param string|null $paramType
             * @param string|null $paramClassName
             * @param             $paramDefault
             * @param bool        $paramAllowsNull
             * @param bool        $paramIsOptional
             * @param bool        $paramIsArray
             * @param int         $paramIndex
             *
             * @return null
             * @throws DiUnresolvableException
             * @throws \ErrorException
             */
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
                // Check if it is Resolvable by Argument Name
                if ($this->diContainer->isResolvable($paramName, $values)) {
                    return $this->diContainer->resolve($paramName, $values);
                }
                // Check if it is resolvable by argument class name hint
                if ($paramClassName !== null && $this->diContainer->isResolvable($paramClassName, $values)) {
                    return $this->diContainer->resolve($paramClassName, $values);
                }

                if (!$paramDefault !== null)
                    return $paramDefault;

                if ($paramIsOptional)
                    return null;
                throw new DiUnresolvableInternalException("Cannot resolve '$paramName' nor '$paramClassName' in argument#$paramIndex");
            }
        });
        $this->add(self::class, new DiValue($this));
    }


    public function has($name) : bool
    {
        return isset ($this->instances[$name]);
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
        if ( ! isset($this->instances[$name]))
            throw new DiUnresolvableException("No factory found to resolve '$name'.");
        return phore_resolve($this->instances[$name]);
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
     * $di->add("param1", "New Value");
     * $di->add("param1", function () { echo "new value"; });
     * $di->add("param1", new Deferred());
     * </Example>
     *
     * @param $name
     * @param $factoryOrValue
     *
     * @return DiContainer
     */
    public function define ($name, $factoryOrValue, $autoWrapCallable=true) : self
    {
        if (is_callable($factoryOrValue) && $autoWrapCallable) {
            $factoryOrValue = phore_deferred($factoryOrValue);
        }
        $this->instances[$name] = $factoryOrValue;
        return $this;
    }

}