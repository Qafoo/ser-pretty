<?php

namespace Qafoo\SerPretty\Node;

use Qafoo\SerPretty\Node;

class ArrayElementNode extends Node
{
    /**
     * @var Node
     */
    private $content;

    /**
     * @var Node
     */
    private $key;

    /**
     * @param Node $content
     * @param Node $key
     */
    public function __construct(Node $content, Node $key)
    {
        $this->content = $content;
        $this->key = $key;
    }

    public function getContent()
    {
        return $this->content;
    }
}
