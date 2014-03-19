<?php

namespace Qafoo\SerPretty;

abstract class Node
{
    /**
     * Returns the content of the node.
     *
     * @return mixed
     */
    abstract public function getContent();
}
