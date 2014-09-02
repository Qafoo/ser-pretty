<?php

namespace Qafoo\SerPretty\Node;

use Qafoo\SerPretty\Node;

class AttributeNode extends Node
{
    const SCOPE_PRIVATE = 'private';
    const SCOPE_PROTECTED = 'protected';
    const SCOPE_PUBLIC = 'public';

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
     * @var AttributeNode::SCOPE_*
     */
    private $scope;

    /**
     * @param Node $content
     * @param Node $propertyName
     */
    public function __construct(Node $content, $className, $propertyName, $scope)
    {
        $this->content = $content;
        $this->className = $className;
        $this->propertyName = $propertyName;
        $this->scope = $scope;
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

    public function getScope()
    {
        return $this->scope;
    }
}
