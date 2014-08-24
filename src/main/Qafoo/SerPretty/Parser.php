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

        return $this->doParse();
    }

    /**
     * @return Node
     */
    private function doParse()
    {

        while ($this->currentIndex < $this->maxIndex) {
            $dataType = $this->serialized[$this->currentIndex];
            $this->debug('Detect: Data type "' . $dataType . '"');

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
                case 'N':
                    return $this->parseNull();
                case 'b':
                    return $this->parseBoolean();

                default:
                    throw new \RuntimeException(
                        $this->errorMessage('Unknown data type "%s"', $dataType)
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
        $this->debug('String: Skip ":"');
        $this->advance(2);

        $string = $this->parseRawString();

        $this->debug('String: Skip ";"');
        $this->advance(1);

        $this->debug('String: Parsed "' . $string . '"');

        return new Node\StringNode($string);
    }

    /**
     * s:0:"";
     *
     * @return string
     */
    private function parseRawString()
    {
        $this->debug('Raw String: Parse byte length');
        $stringLength = $this->parseRawInt();
        $this->debug('Raw String: Byte length is ' . $stringLength);

        $this->debug('Raw String: Skip ":\""');
        $this->advance(3);

        $this->debug('Raw String: Consuming ' . $stringLength . ' bytes');
        $string = '';
        for ($i = 0; $i < $stringLength; $i++) {
            $string .= $this->current();
            $this->advance();
        }
        $this->debug('Raw String: Parsed "' . $string . '"');

        return $string;
    }

    /**
     * i:23;
     *
     * @return Node\IntegerNode
     */
    private function parseInt()
    {
        $this->debug('Int: Skip "i:"');
        $this->advance(2);

        $integer = $this->parseRawInt();

        $this->advance(1);

        return new Node\IntegerNode($integer);
    }

    /**
     * N;
     *
     * @return Node\NullNode
     */
    private function parseNull()
    {
        $this->debug('Null: Skipping ";"');
        $this->advance(1);

        return new Node\NullNode();
    }

    /**
     * b:1;
     *
     * @return Node\BooleanNode
     */
    private function parseBoolean()
    {
        $this->debug('Bool: Skipping "b:"');
        $this->advance(2);

        $value = (bool) $this->parseRawInt();

        $this->advance(1);

        return new Node\BooleanNode($value);
    }

    /**
     * a:2:{i:0;s:3:"foo";i:1;s:3:"bar";}
     *
     * @return Node\ArrayNode
     */
    private function parseArray()
    {
        $this->debug('Array: Skipping "a:"');
        $this->advance(2);

        $arrayCount = $this->parseRawInt();
        $this->debug('Array: Element count ' . $arrayCount);

        $this->debug('Array: Skipping ":{"');
        $this->advance(3);

        $array = array();
        for ($i = 0; $i < $arrayCount; $i++) {
            $this->debug('Array: Parsing element ' . $i);

            $key = $this->doParse();
            $this->debug('Array: Parsed key "' . $key->getContent() . '"');

            $this->advance();

            $value = $this->doParse();
            $this->debug('Array: Parsed value of type "' . gettype($value->getContent()) . '"');

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
        $this->debug('Object: Skipping ":""');
        $this->advance(2);

        $className = $this->parseRawString();
        $this->debug('Object: Class name is "' . $className . '"');

        $this->debug('Object: Skipping "":"');
        $this->advance(2);

        $numAttributes = $this->parseRawInt();
        $this->debug('Object: Has ' . $numAttributes . ' attributes');

        $this->debug('Object: Skipping ":{"');
        $this->advance(3);

        $attributes = array();
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->debug('Object: Parse attribute #' . $i);
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
        $this->debug('Float: Skipping ":"');
        $this->advance(2);

        $this->debug('Float: Parsing');
        $float = '';
        do {
            $float .= $this->current();
            $this->advance();
        } while ($this->current() != ';');

        $this->debug('Float: Parsed "' . $float . '"');
        return new Node\FloatNode((float) $float);
    }

    /**
     * @param Node\StringNode $stringNode
     * @return [<string|null>, <string>]
     */
    private function parseAttributeName(Node\StringNode $stringNode)
    {
        $nameString = $stringNode->getContent();
        $this->debug('Attribute Name: Parse from "' . $nameString . '"');

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
        $this->debug('Raw Int: Parsing');

        $integer = $this->current();
        while (ctype_digit($this->peek())) {
            $this->advance();
            $integer .= $this->current();
        }

        $this->debug('Raw Int: Parsed ' . $integer);

        return (int) $integer;
    }

    /**
     * Advance char cursor by $numChars
     *
     * @param int $numChars
     */
    private function advance($numChars = 1)
    {
        $this->assertInBounds($numChars);
        $this->currentIndex += $numChars;
    }

    /**
     * Return char under cursor
     *
     * @return char
     */
    private function current()
    {
        // $this->debug('Current: ' . $this->serialized[$this->currentIndex]);

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
                $this->errorMessage(
                    'Offset: %s, Max: %s',
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
            $stack = debug_backtrace();

            $nestingLevel = 0;
            for ($i = 0; $i < count($stack); $i++) {
                if ($stack[$i]['function'] === 'parse') {
                    break;
                }
                if (strpos($stack[$i]['function'], 'parse') === 0) {
                    $nestingLevel++;
                }
            }

            printf(
                "%s%s (%s)\n",
                str_repeat(' ', $nestingLevel),
                $message,
                $this->getContext()
            );
        }
    }

    /**
     * @param string $message
     * @param ... $parameters
     */
    private function errorMessage($message)
    {
        $errorMessage = call_user_func_array('sprintf', func_get_args());

        return sprintf('%s (%s)', $errorMessage, $this->getContext());
    }

    private function getContext()
    {
        return sprintf(
            'char "%s" (#%d), context: …%s…',
            $this->serialized[$this->currentIndex],
            $this->currentIndex,
            substr($this->serialized, $this->currentIndex, 7)
        );
    }
}
