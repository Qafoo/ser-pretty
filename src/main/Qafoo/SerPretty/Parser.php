<?php

namespace Qafoo\SerPretty;

class Parser
{
    /**
     * @var int
     */
    private $currentIndex;

    /**
     * @var int
     */
    private $maxIndex;

    /**
     * @var string
     */
    private $serialized;

    /**
     * @var bool
     */
    private $debug = false;

    public function parse($serialized)
    {
        $this->serialized = $serialized;
        $this->currentIndex = 0;
        $this->maxIndex = strlen($serialized);

        return $this->doParse($serialized);
    }

    private function doParse()
    {
        while ($this->currentIndex < $this->maxIndex) {
            switch ($this->serialized[$this->currentIndex]) {
                case 'i':
                    return $this->parseInt();
                case 's':
                    return $this->parseString();
                case 'a':
                    return $this->parseArray();
            }
        }
    }

    /**
     * s:3:"foo";
     */
    private function parseString()
    {
        // Skip ":"
        $this->advance(2);

        $stringLength = $this->parseRawInt();

        // Skip ":\""
        $this->advance(3);

        $string = $this->current();
        for ($i = 1; $i < $stringLength; $i++) {
            $this->advance();
            $string .= $this->current();
        }

        // Skip "\";"
        $this->advance(2);

        return new Node\String($string);
    }

    /**
     * i:23;
     */
    private function parseInt()
    {
        $this->advance(2);

        $integer = $this->parseRawInt();

        $this->advance(1);

        return new Node\Integer($integer);
    }

    /**
     * a:2:{i:0;s:3:"foo";i:1;s:3:"bar";}
     */
    private function parseArray()
    {
        $this->advance(2);

        $arrayCount = $this->parseRawInt();

        $this->advance(3);

        $array = array();
        for ($i = 0; $i < $arrayCount; $i++) {
            $key = $this->doParse();
            $this->advance();

            $value = $this->doParse();
            $this->advance();

            $array[] = new Node\ArrayElementNode($value, $key);
        }
        return new Node\ArrayNode($array);
    }

    private function parseRawInt()
    {
        $integer = $this->current();

        while (ctype_digit($this->peek())) {
            $this->advance();
            $integer .= $this->current();
        }

        return (int) $integer;
    }

    private function advance($numChars = 1)
    {
        $this->debug("Advance $numChars");

        $this->assertInBounds($numChars);
        $this->currentIndex += $numChars;
    }

    private function current()
    {
        $this->debug('Current: ' . $this->serialized[$this->currentIndex]);

        return $this->serialized[$this->currentIndex];
    }

    private function peek($offset = 1)
    {
        $this->assertInBounds($offset);
        return $this->serialized[$this->currentIndex + $offset];
    }

    private function assertInBounds($offset)
    {
        if ($this->currentIndex + $offset >= $this->maxIndex) {
            throw new \OutOfBoundsException(
                sprintf(
                    'Current: %s, Offset: %s, Max: %s',
                    $this->currentIndex,
                    $offset,
                    $this->maxIndex
                )
            );
        }
    }

    private function debug($message)
    {
        if ($this->debug) {
            echo "$message\n";
        }
    }
}
