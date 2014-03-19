<?php

namespace Qafoo\SerPretty\Node;

use Qafoo\SerPretty\Node;

class AttributeNode extends Node
{
    /**
     * @var Node
     */
    private $content;

    /**
     * @var Node
     */
    private $propertyName;

    /**
     * @var Node
     */
    private $className;

    /**
     * @param Node $content
     * @param Node $propertyName
     */
    public function __construct(Node $content, $className, $propertyName)
    {
        $this->content = $content;
        $this->className = $className;
        $this->propertyName = $propertyName;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getPropertyName()
    {
        return $this->propertyName;
    }
}
