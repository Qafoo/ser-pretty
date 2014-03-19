<?php

namespace Qafoo\SerPretty\Node;

use Qafoo\SerPretty\Node;

class Integer extends Node
{
    /**
     * @var string
     */
    private $content;

    /**
     * @param string $content
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
