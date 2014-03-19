<?php

namespace Qafoo\SerPretty\Node;

use Qafoo\SerPretty\Node;

class BooleanNode extends Node
{
    /**
     * @var bool
     */
    private $content;

    /**
     * @param bool $content
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
