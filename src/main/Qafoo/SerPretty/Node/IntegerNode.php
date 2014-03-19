<?php

namespace Qafoo\SerPretty\Node;

use Qafoo\SerPretty\Node;

class IntegerNode extends Node
{
    /**
     * @var int
     */
    private $content;

    /**
     * @param int $content
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
