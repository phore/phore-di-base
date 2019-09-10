<?php


namespace Test;


use DateTime;
use PHPUnit\Framework\TestCase;
require_once "../../src/functions.php";

class PhoreVarTest extends TestCase
{
    /**
     * @dataProvider basicTypesProvider
     */
    public function testReturnsBasicTypes($type,$expected)
    {
        //Assert
        $this->assertEquals($expected,phore_var($type));
    }


    public function basicTypesProvider()
    {
        return [
            [true, "[boolean:true]"],
            [1, "[integer:1]"],
            [1.11, "[double:1.11]"],
            ["test", "[string:test]"],
            [[1], "[array:1]"],
            [new DateTime(), "[object:DateTime]"],
            [fopen("foo", "w"), "[resource:stream]"],
            [null,"[NULL:]"]
        ];
    }
}