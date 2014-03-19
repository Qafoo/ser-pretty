<?php

namespace Qafoo\SerPretty\Node;

use Qafoo\SerPretty\Node;

class ObjectNode extends Node
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * @param array $attributes
     * @param string $className
     */
    public function __construct(array $attributes, $className)
    {
        $this->attributes = $attributes;
        $this->className = $className;
    }

    public function getContent()
    {
        return $this->attributes;
    }

    public function getClassName()
    {
        return $this->className;
    }
}
