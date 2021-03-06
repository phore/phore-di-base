<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 02.12.16
     * Time: 17:27
     */

    namespace Phore\Test\Di;


    use Phore\Di\Caller\PhoreBaseDiCaller;
    use PHPUnit\Framework\TestCase;

    class PhoreBaseDiCallerTest extends TestCase
    {


        public function testInvoke () {
            $c = new PhoreBaseDiCaller();
            self::assertEquals("p1p2p3", $c(
                    function ($a, $b, $c) {
                        return $a . $b . $c;
                    },
                    ["a"=>"p1", "b"=>"p2", "c" => "p3"] ));
        }

    }
