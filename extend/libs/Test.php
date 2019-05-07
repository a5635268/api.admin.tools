<?php

namespace libs;

use traits\Singleton;

class Test{
    use Singleton;

    private $name;
    private $age;

    private function __construct($name,$age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    private function func($a,$b,$c)
    {
        echo $a,$b,$c;
    }

    public function __call($name , $arguments)
    {
        $this->func(...$arguments);
    }
}