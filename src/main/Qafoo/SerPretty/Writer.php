<?php

namespace Qafoo\SerPretty;

abstract class Writer
{
    /**
     * Returns a string representation of $node
     *
     * @param Node $node
     * @return string
     */
    abstract public function write(Node $node);
}
