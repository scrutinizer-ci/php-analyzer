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

namespace Scrutinizer\Tests\PhpAnalyzer\PhpParser;

use Scrutinizer\PhpAnalyzer\PhpParser\DocCommentParser;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;

class DocCommentParserTest extends \PHPUnit_Framework_TestCase
{
    private $registry;
    private $parser;

    public function testParseParam()
    {
        $param = $this->createParam('foo', '@param string $foo');
        $scannedType = $this->parser->getTypeFromParamAnnotation($param, 'foo');

        $this->assertNotNull($scannedType);
        $this->assertTrue($this->registry->getNativeType('string')->equals($scannedType));
    }

    public function testParseParam2()
    {
        $param = $this->createParam('foo', '@param foo string');
        $scannedType = $this->parser->getTypeFromParamAnnotation($param, 'foo');

        $this->assertNotNull($scannedType);
        $this->assertTrue($this->registry->getNativeType('string')->equals($scannedType));
    }

    public function testWithNamespace()
    {
        $this->parser->setImportedNamespaces(array('' => 'Foo\Bar'));
        $type = $this->getTypeFromParam('Baz');

        $this->assertNotNull($type);
        $this->assertTrue($this->registry->getClassOrCreate('Foo\Bar\Baz')->equals($type));
    }

    public function testWithAbsoluteNamespace()
    {
        $this->parser->setImportedNamespaces(array('' => 'Foo'));
        $type = $this->getTypeFromParam('\ReflectionClass');

        $this->assertNotNull($type);
        $this->assertTrue($this->registry->getClassOrCreate('ReflectionClass')->equals($type));
    }

    public function testWithImportedNamespace()
    {
        $this->parser->setImportedNamespaces(array('' => 'Foo', 'Bar' => 'Baz\Moo'));
        $type = $this->getTypeFromParam('Bar');

        $this->assertNotNull($type);
        $this->assertTrue($this->registry->getClassOrCreate('Baz\Moo')->equals($type));
    }

    public function testSelfReferencingTypes()
    {
        $this->parser->setCurrentClassName('Foo');

        $type = $this->getTypeFromParam('self');
        $this->assertNotNull($type);
        $this->assertTrue($this->registry->getClassOrCreate('Foo')->equals($type));

        $type = $this->getTypeFromParam('$this');
        $this->assertNotNull($type);
        $this->assertTrue($this->registry->getClassOrCreate('Foo')->equals($type));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage "self" is only available from within classes.
     */
    public function testSelfReferencingTypesFromOutsideOfClasses()
    {
        $this->parser->getType('self');
    }

    /**
     * @dataProvider getParseTests
     */
    public function testParse($docType, $expectedType, array $importedNamespaces = array())
    {
        $this->parser->setImportedNamespaces($importedNamespaces);
        $type = $this->parser->getType($docType);
        $expectedType = $this->registry->resolveType($expectedType);

        $this->assertNotNull($type);
        $this->assertTrue($expectedType->equals($type), sprintf('Actual type "%s" does not equal expected type "%s".', $type, $expectedType));
    }

    public function getParseTests()
    {
        $tests = array();

        $tests[] = array('array', 'array');
        $tests[] = array('int[]', 'array<integer>');
        $tests[] = array('(int|string)[]', 'array<integer|string>');
        $tests[] = array('array<*,string>', 'array<integer|string,string>');
        $tests[] = array('array<string,integer>', 'array<string,integer>');
        $tests[] = array('array<string,null|scalar|array>', 'array<string,null|scalar|array>');
        $tests[] = array('Foo\Bar\Baz', 'object<Foo\Bar\Baz>');
        $tests[] = array('Foo\Bar', 'object<Baz\Foo\Bar>', array('' => 'Baz'));
        $tests[] = array('Foo', 'object<Foo>');
        $tests[] = array('int[optional]', 'integer');

        return $tests;
    }

    /**
     * @dataProvider getInvalidTypeTests
     * @expectedException \RuntimeException
     */
    public function testParseThrowsExceptionForInvalidType($docType)
    {
        $this->parser->getType($docType);
    }

    public function getInvalidTypeTests()
    {
        $tests = array();
        $tests[] = array('Sonata\AdminBundle\Model\ModelManagerInterface;');
        $tests[] = array('array<Foo|Bar;>');

        return $tests;
    }

    protected function setUp()
    {
        $this->registry = new TypeRegistry();
        $this->parser = new DocCommentParser($this->registry);
    }

    private function getTypeFromParam($str)
    {
        $param = $this->createParam('x', '@param x '.$str);

        return $this->parser->getTypeFromParamAnnotation($param, 'x');
    }

    private function createParam($name, $docComment)
    {
        $function = new \PHPParser_Node_Stmt_Function('Foo', array(
            'params' => array(
                $param = new \PHPParser_Node_Param($name),
            ),
        ), -1, $docComment);
        $param->setAttribute('parent', $function);

        return $param;
    }
}