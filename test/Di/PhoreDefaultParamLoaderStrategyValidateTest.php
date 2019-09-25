<?php

namespace Di;

use InvalidArgumentException;
use Phore\Di\Builder\PhoreDefaultParamLoaderStrategy;
use Phore\Di\Container\DiContainer;
use Phore\Di\Container\DiUnresolvableException;
use PHPUnit\Framework\TestCase;


/**
 * Class PhoreDefaultParamLoaderStrategyTest
 * @package Di
 * @internal
 */
class PhoreDefaultParamLoaderStrategyValidateTest extends TestCase
{
    /**
     * @var PhoreDefaultParamLoaderStrategy
     */
    public $sut;

    /**
     * @var DiContainer
     */
    public $diContainer;

    protected function setUp(): void
    {
        $this->diContainer = new DiContainer();
        $this->sut = new PhoreDefaultParamLoaderStrategy($this->diContainer);
    }



}

