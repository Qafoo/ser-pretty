<?php

namespace Qafoo\SerPretty\Writer;

use Qafoo\SerPretty\Writer;
use Qafoo\SerPretty\Node;

class SimpleTextWriter extends Writer
{
    public function write(Node $node)
    {
        switch(get_class($node)) {
            case 'Qafoo\\SerPretty\\Node\\StringNode':
                return $this->writeString($node);

            case 'Qafoo\\SerPretty\\Node\\IntegerNode':
                return $this->writeInteger($node);

            case 'Qafoo\\SerPretty\\Node\\FloatNode':
                return $this->writeFloat($node);

            case 'Qafoo\\SerPretty\\Node\\ArrayNode':
                return $this->writeArray($node);

            case 'Qafoo\\SerPretty\\Node\\ObjectNode':
                return $this->writeObject($node);

            case 'Qafoo\\SerPretty\\Node\\NullNode':
                return $this->writeNull();

            case 'Qafoo\\SerPretty\\Node\\BooleanNode':
                return $this->writeBoolean($node);

            case 'Qafoo\\SerPretty\\Node\\SerializableObjectNode':
                return $this->writeSerializableObject($node);

            case 'Qafoo\\SerPretty\\Node\\ReferenceNode':
                return $this->writeReference($node);

            default:
                throw new \RuntimeException(
                    sprintf(
                        'Unknown node type "%s"',
                        get_class($node)
                    )
                );
        }
    }

    private function writeString(Node\StringNode $stringNode)
    {
        return sprintf(
            'string(%s) "%s"',
            strlen($stringNode->getContent()),
            $stringNode->getContent()
        );
    }

    private function writeInteger(Node\IntegerNode $integerNode)
    {
        return sprintf(
            'int(%s)',
            $integerNode->getContent()
        );
    }

    private function writeNull()
    {
        return 'null';
    }

    private function writeBoolean(Node\BooleanNode $integerNode)
    {
        return sprintf(
            'bool(%s)',
            $integerNode->getContent() ? 'true' : 'false'
        );
    }

    private function writeFloat(Node\FloatNode $floatNode)
    {
        return sprintf(
            'double(%s)',
            $floatNode->getContent()
        );
    }

    private function writeArray(Node\ArrayNode $arrayNode)
    {
        return sprintf(
            "array(%s) {\n%s\n}",
            count($arrayNode->getContent()),
            $this->indent(
                implode(
                    "\n",
                    array_map(
                        array($this, 'writeArrayElement'),
                        $arrayNode->getContent()
                    )
                )
            )
        );
    }

    public function writeArrayElement(Node\ArrayElementNode $elementNode)
    {
        $key = $elementNode->getKey();
        $val = $elementNode->getContent();

        return sprintf(
            "%s =>\n%s",
            ($key instanceof Node\IntegerNode
                ? sprintf('[%s]', $key->getContent())
                : sprintf('\'%s\'', $key->getContent())),
            $this->write($val)
        );
    }

    /**
     * class Qafoo\SerPretty\TestClass#1 (2) {
     *    private $foo =>
     *    int(23)
     *    public $bar =>
     *    string(3) "baz"
     *  }
     */
    private function writeObject(Node\ObjectNode $objectNode)
    {
        return sprintf(
            "class %s (%s) {\n%s\n}",
            $objectNode->getClassName(),
            count($objectNode->getContent()),
            $this->indent(
                implode(
                    "\n",
                    array_map(
                        array($this, 'writeAttribute'),
                        $objectNode->getContent()
                    )
                )
            )
        );
    }

    public function writeAttribute(Node\AttributeNode $attributeNode)
    {
        $class = $attributeNode->getClassName();
        $property = $attributeNode->getPropertyName();
        $val = $attributeNode->getContent();
        $scope = $attributeNode->getScope();

        return sprintf(
            "%s $%s =>\n%s",
            $scope,
            $property,
            $this->write($val)
        );
    }

    /**
     * @param Node\ReferenceNode $objectNode
     * @return string
     */
    private function writeReference(Node\ReferenceNode $objectNode)
    {
        return sprintf(
            "reference to %d",
            $objectNode->getContent()
        );
    }

    /**
     * @param Qafoo\SerPretty\Node\SerializableObjectNode $objectNode
     * @return string
     */
    private function writeSerializableObject(Node\SerializableObjectNode $objectNode)
    {
        return sprintf(
            "class %s (custom) {\n%s\n}",
            $objectNode->getClassName(),
            $this->indent(
                $this->write($objectNode->getContent())
            )
        );
    }

    private function indent($string)
    {
        return implode(
            "\n",
            array_map(
                function ($line) {
                    return '  ' . $line;
                },
                explode("\n", $string)
            )
        );
    }
}
