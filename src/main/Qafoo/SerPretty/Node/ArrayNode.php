<?php

namespace Qafoo\SerPretty\Node;

use Qafoo\SerPretty\Node;

class ArrayNode extends Node
{
    /**
     * @var array
     */
    private $content;

    /**
     * @param array $content
     */
    public function __construct(array $content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}
