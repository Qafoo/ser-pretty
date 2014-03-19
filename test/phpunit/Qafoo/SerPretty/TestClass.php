<?php

namespace Qafoo\SerPretty;

class TestClass
{
    private $foo = 23;

    public $bar = 'baz';

    public function setFoo($fooValue)
    {
        $this->foo = $fooValue;
    }

    public function setBar($barValue)
    {
        $this->bar = $barValue;
    }
}
