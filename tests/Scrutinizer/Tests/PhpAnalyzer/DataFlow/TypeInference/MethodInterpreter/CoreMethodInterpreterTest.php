<?php

/*
 * Copyright 2013 Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Scrutinizer\Tests\PhpAnalyzer\DataFlow\TypeInference\MethodInterpreter;

class CoreMethodInterpreterTest extends \PHPUnit_Framework_TestCase
{
    private $registry;
    private $interpreter;

    /**
     * @dataProvider getPreciserTests
     */
    public function testGetPreciserType($qualifiedName, array $rawArgValues, array $rawArgTypes, $expectedType)
    {
        $argValues = array_map(array($this, 'resolveArgValue'), $rawArgValues);
        $argTypes = array_map(array($this->registry, 'resolveType'), $rawArgTypes);

        $method = $this->getMock('Scrutinizer\PhpAnalyzer\Model\ContainerMethodInterface');
        $method->expects($this->any())
            ->method('getQualifiedName')
            ->will($this->returnValue($qualifiedName));

        $restrictedType = $this->interpreter->getPreciserMethodReturnTypeKnowingArguments($method, $argValues, $argTypes);

        if (null === $expectedType) {
            $this->assertTrue(null === $restrictedType, sprintf('Expected null, but got type "%s".', $restrictedType));
        } else {
            $this->assertNotNull($restrictedType, sprintf('Expected type "%s", but got null.', $expectedType));

            $expectedType = $this->registry->resolveType($expectedType);
            $this->assertTrue($expectedType->equals($restrictedType), sprintf('Failed to assert that expected type "%s" equals actual type "%s".', $expectedType, $restrictedType));
        }
    }

    public function getPreciserTests()
    {
        // Qualified Name | Arg Values | Arg Types | Expected Type
        $tests = array();

        $tests[] = array('SimpleXMLElement::asXml', array(), array(), 'string|false');
        $tests[] = array('SIMpleXmLElemeNT::AsXmL', array(), array(), 'string|false');
        $tests[] = array('SimpleXMLElement::asXml', array('foo'), array('string'), 'boolean');

        return $tests;
    }

    private function resolveArgValue($value)
    {
        switch ($value) {
            case 'false':
            case 'null':
            case 'true':
                return new \PHPParser_Node_Expr_ConstFetch(new \PHPParser_Node_Name(array($value)));

            default:
                switch ($value[0]) {
                    case '$':
                        return new \PHPParser_Node_Expr_Variable(substr($value, 1));

                    default:
                        return new \PHPParser_Node_Scalar_String($value);
                }
        }
    }

    protected function setUp()
    {
        $this->registry = new \Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry();
        $this->interpreter = new \Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\MethodInterpreter\CoreMethodInterpreter($this->registry);
    }
}