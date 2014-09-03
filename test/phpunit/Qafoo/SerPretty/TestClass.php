<?php

namespace Qafoo\SerPretty;

class TestClass
{
    private $foo = 23;

    public $bar = 'baz';

    protected $baz = true;

    public function setFoo($fooValue)
    {
        $this->foo = $fooValue;
    }

    public function setBar($barValue)
    {
        $this->bar = $barValue;
    }

    public function setBaz($bazValue)
    {
        $this->baz = $bazValue;
    }
}
