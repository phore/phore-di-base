<?php



/**
 * Class Demo
 */
class Demo {

    /**
     * @var callable
     */
    public $cb;

    public function __construct(Demo $a1 = null)
    {
        $this->cb = function ($a, $b) {
            echo "L-CALLED $a $b ";
        };
    }

    public function fn(int $i)
    {
        echo "fn called";
    }

    public function a()
    {
        $this->fn(1);

        $param = ["bb", "BBB"];

        ($this->cb)(...$param);
        //($this->cb)();


        $z = ([$this, "fn"])(1);

    }


    public static function B() {
        echo "B";
    }
}

$obj = new Demo();
$obj->a();


function xy() { echo "XY called"; };

("exec")("echo wurst");

([$obj, "a"])();

([Demo::class, "a"])();
