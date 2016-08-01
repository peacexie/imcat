<?php
require(dirname(__FILE__).'/_config.php'); 
use Vdemo\dir1\Test4;


class Base {
    public function bfunc() {
        echo 'Base::bfunc <br>';
    }
    public function sayHello() {
        echo 'Hello ';
    }
}

trait SayWorld {
    public function sayHello() {
        parent::sayHello();
        echo 'World!';
    }
}

class MyHelloWorld extends Base {
    use SayWorld;
}

$o = new MyHelloWorld();
$o->bfunc();
$o->sayHello();
echo "<hr>";


// basDebug
echo basDebug::runInfo();

?>
