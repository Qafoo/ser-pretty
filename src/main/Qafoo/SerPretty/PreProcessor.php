<?php

namespace Qafoo\SerPretty;

abstract class PreProcessor
{
    /**
     * Pre-processes the given $inputString and returns it.
     *
     * @param string $inputString
     * @return string
     */
    abstract public function process($inputString);
}
