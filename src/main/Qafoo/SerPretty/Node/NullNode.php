<?php

namespace Qafoo\SerPretty\Node;

use Qafoo\SerPretty\Node;

class NullNode extends Node
{
    public function getContent()
    {
        return null;
    }
}
