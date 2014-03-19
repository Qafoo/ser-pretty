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

    /**
     * @param bool $debug
     */
    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * @param string $serialized
     * @return Node
     */
    public function parse($serialized)
    {
        $this->serialized = $serialized;
        $this->currentIndex = 0;
        $this->maxIndex = strlen($serialized);

        return $this->doParse($serialized);
    }

    /**
     * @return Node
     */
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
                case 'd':
                    return $this->parseFloat();

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
     *
     * @return Node\StringNode
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

    /**
     * @return string
     */
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
     *
     * @return Node\IntegerNode
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
     *
     * @return Node\ArrayNode
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
     *
     * @return Node\ObjectNode
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

    /**
     * d:42.5;
     */
    private function parseFloat()
    {
        $this->advance(2);

        $float = '';
        do {
            $float .= $this->current();
            $this->advance();
        } while ($this->current() != ';');

        return new Node\FloatNode((float) $float);
    }

    /**
     * @param Node\StringNode $stringNode
     * @return [<string|null>, <string>]
     */
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

    /**
     * @return int
     */
    private function parseRawInt()
    {
        $integer = $this->current();

        while (ctype_digit($this->peek())) {
            $this->advance();
            $integer .= $this->current();
        }

        return (int) $integer;
    }

    /**
     * Advance char cursor by $numChars
     *
     * @param int $numChars
     */
    private function advance($numChars = 1)
    {
        $this->debug("Advance $numChars");

        $this->assertInBounds($numChars);
        $this->currentIndex += $numChars;

        // Debugging
        $this->current();
    }

    /**
     * Return char under cursor
     *
     * @return char
     */
    private function current()
    {
        $this->debug('Current: ' . $this->serialized[$this->currentIndex]);

        return $this->serialized[$this->currentIndex];
    }

    /**
     * Peek $offset characters ahead of cursor
     *
     * @param int $offset
     * @return char
     */
    private function peek($offset = 1)
    {
        $this->assertInBounds($offset);
        return $this->serialized[$this->currentIndex + $offset];
    }

    /**
     * Assert $offset is in bounds from current cursor position
     *
     * @param int $offset
     */
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

    /**
     * Output debug message, if debugging is enabled
     *
     * @param string $message
     */
    private function debug($message)
    {
        if ($this->debug) {
            echo "$message\n";
        }
    }
}
