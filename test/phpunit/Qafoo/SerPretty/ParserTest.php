<?php

namespace Qafoo\SerPretty;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    private $parser;

    public function setUp()
    {
        $this->parser = new Parser();
    }

    public function testParseNull()
    {
        $this->assertEquals(
            new Node\NullNode(),
            $this->parser->parse(serialize(null))
        );
    }

    public function testParseBoolean()
    {
        $this->assertEquals(
            new Node\BooleanNode(true),
            $this->parser->parse(serialize(true))
        );

        $this->assertEquals(
            new Node\BooleanNode(false),
            $this->parser->parse(serialize(false))
        );
    }

    public function testParseString()
    {
        $this->assertEquals(
            new Node\StringNode('foo'),
            $this->parser->parse(serialize('foo'))
        );
    }

    public function testParseEmptyString()
    {
        $this->assertEquals(
            new Node\StringNode(''),
            $this->parser->parse(serialize(''))
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

    public function testParseFloat()
    {
        $this->assertEquals(
            new Node\FloatNode(42.5),
            $this->parser->parse(serialize(42.5))
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
                        new Node\FloatNode(42.5),
                        new Node\IntegerNode(1)
                    )
                )
            ),
            $this->parser->parse(
                serialize(
                    array('foo', 42.5)
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

    public function testParseObjectReference()
    {
        $testObj = new \stdClass();
        $testObj->a = 1;
        $refObj = new \stdClass();
        $refObj->ref = $testObj;
        $this->parser = new Parser(TRUE);

        echo serialize(array($refObj, $testObj));
        $this->assertEquals(
            new Node\ArrayNode(
                array(
                    new Node\ArrayElementNode(
                        new Node\ObjectNode(
                            array(
                                new Node\AttributeNode(
                                    new Node\ObjectNode(
                                        array(
                                            new Node\AttributeNode(
                                                new Node\IntegerNode(1),
                                                null,
                                                'a',
                                                Node\AttributeNode::SCOPE_PUBLIC
                                            )
                                        ),
                                        'stdClass'
                                    ),
                                    null,
                                    'ref',
                                    Node\AttributeNode::SCOPE_PUBLIC
                                )
                            ),
                            'stdClass'
                        ),
                        new Node\IntegerNode(0)
                    ),
                    new Node\ArrayElementNode(
                        new Node\ReferenceNode(3),
                        new Node\IntegerNode(1)
                    )
                )
            ),
            $this->parser->parse(
                serialize(array($refObj, $testObj))
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
                        'foo',
                        Node\AttributeNode::SCOPE_PRIVATE
                    ),
                    new Node\AttributeNode(
                        new Node\StringNode('baz'),
                        null,
                        'bar',
                        Node\AttributeNode::SCOPE_PUBLIC
                    ),
                    new Node\AttributeNode(
                        new Node\BooleanNode(true),
                        '*',
                        'baz',
                        Node\AttributeNode::SCOPE_PROTECTED
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
                                    'foo',
                                    Node\AttributeNode::SCOPE_PRIVATE
                                ),
                                new Node\AttributeNode(
                                    new Node\ObjectNode(
                                        array(),
                                        'stdClass'
                                    ),
                                    null,
                                    'bar',
                                    Node\AttributeNode::SCOPE_PUBLIC
                                ),
                                new Node\AttributeNode(
                                    new Node\BooleanNode(true),
                                    '*',
                                    'baz',
                                    Node\AttributeNode::SCOPE_PROTECTED
                                )
                            ),
                            'Qafoo\\SerPretty\\TestClass'
                        ),
                        'Qafoo\SerPretty\TestClass',
                        'foo',
                        Node\AttributeNode::SCOPE_PRIVATE
                    ),
                    new Node\AttributeNode(
                        new Node\IntegerNode(23),
                        null,
                        'bar',
                        Node\AttributeNode::SCOPE_PUBLIC
                    ),
                    new Node\AttributeNode(
                        new Node\BooleanNode(true),
                        '*',
                        'baz',
                        Node\AttributeNode::SCOPE_PROTECTED
                    )
                ),
                'Qafoo\\SerPretty\\TestClass'
            ),
            $this->parser->parse(serialize($complexObject))
        );
    }

    public function testParseSerializableObject()
    {
        $serializableObject = new SerializableTestClass();

        $this->assertEquals(
            new Node\SerializableObjectNode(
                new Node\IntegerNode(23),
                'Qafoo\\SerPretty\\SerializableTestClass'
            ),
            $this->parser->parse(serialize($serializableObject))
        );
    }

    public function testParseSerializableObjectInCombination()
    {
        $arrayWithSerializable = array(
            'foo' => new SerializableTestClass(),
            'bar' => 'b'
        );

        $this->assertEquals(
            new Node\ArrayNode(
                array(
                    new Node\ArrayElementNode(
                        new Node\SerializableObjectNode(
                            new Node\IntegerNode(23),
                            'Qafoo\\SerPretty\\SerializableTestClass'
                        ),
                        new Node\StringNode('foo')
                    ),
                    new Node\ArrayElementNode(
                        new Node\StringNode('b'),
                        new Node\StringNode('bar')
                    )
                )
            ),
            $this->parser->parse(serialize($arrayWithSerializable))
        );
    }
}
