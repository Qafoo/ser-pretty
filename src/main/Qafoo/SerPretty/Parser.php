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
            $dataType = $this->serialized[$this->currentIndex];
            switch ($dataType) {
                case 'i':
                    return $this->parseInt();
                case 's':
                    return $this->parseString();
                case 'a':
                    return $this->parseArray();
                case 'O':
                    return $this->parseObject();

                default:
                    throw new \RuntimeException(
                        sprintf(
                            'Unknown data type "%s"',
                            $dataType
                        )
                    );
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

        $string = $this->parseRawString();

        // Skip "\";"
        $this->advance(2);

        return new Node\StringNode($string);
    }

    private function parseRawString()
    {
        $stringLength = $this->parseRawInt();

        // Skip ":\""
        $this->advance(3);

        $string = $this->current();
        for ($i = 1; $i < $stringLength; $i++) {
            $this->advance();
            $string .= $this->current();
        }

        return $string;
    }

    /**
     * i:23;
     */
    private function parseInt()
    {
        $this->advance(2);

        $integer = $this->parseRawInt();

        $this->advance(1);

        return new Node\IntegerNode($integer);
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

    /**
     * O:25:"Qafoo\SerPretty\TestClass":2:{s:30:"Qafoo\SerPretty\TestClassfoo";i:0;s:3:"bar";s:3:"baz";}
     */
    private function parseObject()
    {
        $this->advance(2);

        $className = $this->parseRawString();

        $this->advance(3);

        $numAttributes = $this->parseRawInt();

        $this->advance(3);

        $attributes = array();
        for ($i = 0; $i < $numAttributes; $i++) {
            list($class, $name) = $this->parseAttributeName(
                $this->doParse()
            );

            $this->advance();

            $value = $this->doParse();

            $this->advance();

            $attributes[] = new Node\AttributeNode($value, $class, $name);
        }

        return new Node\ObjectNode($attributes, $className);
    }

    private function parseAttributeName(Node\StringNode $stringNode)
    {
        $nameString = $stringNode->getContent();

        if (substr($nameString, 0, 1) === "\0") {
            $nameString = substr($nameString, 1);
        }

        if (strpos($nameString, "\0")) {
            return array(
                substr($nameString, 0, strpos($nameString, "\000")),
                substr($nameString, strpos($nameString, "\000") + 1)
            );
        }

        return array(
            null,
            $nameString
        );
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

        // Debugging
        $this->current();
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
