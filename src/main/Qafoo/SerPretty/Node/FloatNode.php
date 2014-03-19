<?php

namespace Qafoo\SerPretty\Node;

use Qafoo\SerPretty\Node;

class FloatNode extends Node
{
    /**
     * @var float
     */
    private $content;

    /**
     * @param float $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}
