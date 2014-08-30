<?php

namespace Qafoo\SerPretty\Node;
use Qafoo\SerPretty\Node;

class SerializableObjectNode extends Node
{
    /**
     * @var mixed
     */
    private $content;

    /**
     * @var string
     */
    private $className;

    /**
     * @param mixed $content
     */
    public function __construct($content, $className)
    {
        $this->content = $content;
        $this->className = $className;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    public function getClassName()
    {
        return $this->className;
    }
}
