<?php
namespace Qafoo\SerPretty\Node;

use Qafoo\SerPretty\Node;

class ReferenceNode extends Node {

    private $reference;

    /**
     * @param int $reference
     */
    public function __construct($reference)
    {
        $this->reference = $reference;
    }

    public function getContent()
    {
        return $this->reference;
    }

}
 