<?php

namespace Qafoo\SerPretty;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    private $parser;

    public function setUp()
    {
        $this->parser = new Parser();
    }

    public function testParseString()
    {
        $this->assertEquals(
            new Node\StringNode('foo'),
            $this->parser->parse(serialize('foo'))
        );
    }

    public function testParseInteger()
    {
        $this->assertEquals(
            new Node\IntegerNode(23),
            $this->parser->parse(serialize(23))
        );
    }

    public function testParseSingleDigitInteger()
    {
        $this->assertEquals(
            new Node\IntegerNode(7),
            $this->parser->parse(serialize(7))
        );
    }

    public function testParseArray()
    {
        $this->assertEquals(
            new Node\ArrayNode(
                array(
                    new Node\ArrayElementNode(
                        new Node\StringNode('foo'),
                        new Node\IntegerNode(0)
                    ),
                    new Node\ArrayElementNode(
                        new Node\StringNode('bar'),
                        new Node\IntegerNode(1)
                    )
                )
            ),
            $this->parser->parse(
                serialize(
                    array('foo', 'bar')
                )
            )
        );
    }

    public function testParseAssociativeArray()
    {
        $this->assertEquals(
            new Node\ArrayNode(
                array(
                    new Node\ArrayElementNode(
                        new Node\StringNode('foo'),
                        new Node\StringNode('a')
                    ),
                    new Node\ArrayElementNode(
                        new Node\StringNode('bar'),
                        new Node\StringNode('b')
                    )
                )
            ),
            $this->parser->parse(
                serialize(
                    array('a' => 'foo', 'b' => 'bar')
                )
            )
        );
    }

    public function testParseNestedArray()
    {
        $this->assertEquals(
            new Node\ArrayNode(
                array(
                    new Node\ArrayElementNode(
                        new Node\ArrayNode(
                            array(
                                new Node\ArrayElementNode(
                                    new Node\StringNode('foo'),
                                    new Node\IntegerNode(0)
                                ),
                            )
                        ),
                        new Node\StringNode('a')
                    ),
                    new Node\ArrayElementNode(
                        new Node\ArrayNode(
                            array(
                                new Node\ArrayElementNode(
                                    new Node\StringNode('bar'),
                                    new Node\IntegerNode(23)
                                ),
                            )
                        ),
                        new Node\StringNode('b')
                    )
                )
            ),
            $this->parser->parse(
                serialize(
                    array(
                        'a' => array('foo'),
                        'b' => array(23 => 'bar'))
                )
            )
        );
    }

    public function testParseObject()
    {
        $testObj = new TestClass();

        $this->assertEquals(
            new Node\ObjectNode(
                array(
                    new Node\AttributeNode(
                        new Node\IntegerNode(23),
                        'Qafoo\SerPretty\TestClass',
                        'foo'
                    ),
                    new Node\AttributeNode(
                        new Node\StringNode('baz'),
                        null,
                        'bar'
                    )
                ),
                'Qafoo\\SerPretty\\TestClass'
            ),
            $this->parser->parse(
                serialize($testObj)
            )
        );
    }

    public function testParseComplexObject()
    {
        $complexObject = new TestClass();

        $childObject = new TestClass();
        $childObject->setFoo(array('foobar', 'foo' => 'bar'));
        $childObject->setBar(new \stdClass());

        $complexObject->setFoo($childObject);
        $complexObject->setBar(23);

        $this->assertEquals(
            // $complexObject
            new Node\ObjectNode(
                array(
                    new Node\AttributeNode(
                        // $childObject
                        new Node\ObjectNode(
                            array(
                                new Node\AttributeNode(
                                    new Node\ArrayNode(
                                        array(
                                            new Node\ArrayElementNode(
                                                new Node\StringNode('foobar'),
                                                new Node\IntegerNode(0)
                                            ),
                                            new Node\ArrayElementNode(
                                                new Node\StringNode('bar'),
                                                new Node\StringNode('foo')
                                            ),
                                        )
                                    ),
                                    'Qafoo\SerPretty\TestClass',
                                    'foo'
                                ),
                                new Node\AttributeNode(
                                    new Node\ObjectNode(
                                        array(),
                                        'stdClass'
                                    ),
                                    null,
                                    'bar'
                                ),
                            ),
                            'Qafoo\\SerPretty\\TestClass'
                        ),
                        'Qafoo\SerPretty\TestClass',
                        'foo'
                    ),
                    new Node\AttributeNode(
                        new Node\IntegerNode(23),
                        null,
                        'bar'
                    )
                ),
                'Qafoo\\SerPretty\\TestClass'
            ),
            $this->parser->parse(serialize($complexObject))
        );
    }
}
