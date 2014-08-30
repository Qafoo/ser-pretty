<?php

namespace Qafoo\SerPretty;

class SerializableTestClass implements \Serializable
{
    private $foo = 23;

    public function setFoo($fooValue)
    {
        $this->foo = $fooValue;
    }

    public function serialize()
    {
        return serialize($this->foo);
    }

    public function unserialize($serialized)
    {
        $this->foo = unserialize($serialized);
    }
}
