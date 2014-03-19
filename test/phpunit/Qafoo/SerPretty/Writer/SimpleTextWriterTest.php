<?php

namespace Qafoo\SerPretty\Writer;

use Qafoo\SerPretty\Node;

class SimpleTextWriterTest extends \PHPUnit_Framework_TestCase
{
    private $writer;

    public function setUp()
    {
        $this->writer = new SimpleTextWriter();
    }

    public function testWriteString()
    {
        $this->assertEquals(
            'string(3) "foo"',
            $this->writer->write(new Node\StringNode('foo'))
        );
    }

    public function testWriteInteger()
    {
        $this->assertEquals(
            'int(23)',
            $this->writer->write(new Node\IntegerNode(23))
        );
    }

    public function testWriteFloat()
    {
        $this->assertEquals(
            'double(42.5)',
            $this->writer->write(new Node\FloatNode(42.5))
        );
    }

    public function testWriteBoolean()
    {
        $this->assertEquals(
            'bool(true)',
            $this->writer->write(new Node\BooleanNode(true))
        );
    }

    public function testWriteNull()
    {
        $this->assertEquals(
            'null',
            $this->writer->write(new Node\NullNode(true))
        );
    }

    public function testWriteArray()
    {
        $this->assertEquals(
            "array(2) {\n  'foo' =>\n  string(3) \"bar\"\n  [0] =>\n  int(23)\n}",
            $this->writer->write(
                new Node\ArrayNode(
                    array(
                        new Node\ArrayElementNode(
                            new Node\StringNode('bar'),
                            new Node\StringNode('foo')
                        ),
                        new Node\ArrayElementNode(
                            new Node\IntegerNode(23),
                            new Node\IntegerNode(0)
                        )
                    )
                )
            )
        );
    }

    public function testWriteObject()
    {
        $this->assertEquals(
            "class Qafoo\SerPretty\TestClass (2) {\n  public \$foo =>\n  int(23)\n  private \$bar =>\n  string(3) \"baz\"\n}",
            $this->writer->write(
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
                )
            )
        );
    }
}
