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

namespace Scrutinizer\Tests\PhpAnalyzer\Pass;

use Scrutinizer\PhpAnalyzer\Analyzer;
use Scrutinizer\PhpAnalyzer\PassConfig;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Util\TestUtils;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class TypeInferencePassTest extends BaseAnalyzingPassTest
{
    /**
     * @dataProvider getPropertyTests
     */
    public function testProperties($name)
    {
        $ast = $this->analyzeAst('TypeInference/'.$name.'.php');

        $nodes = \PHPParser_Finder::create($ast)->find('Expr_PropertyFetch[name=foo]');
        $this->assertSame(2, count($nodes), 'There are 4 accesses to the "foo" property.');

        $expectedType = $this->registry->getClass('Foo');
        foreach ($nodes as $node) {
            $this->assertTrue($expectedType->equals($actualType = $node->getAttribute('type')), 'Expected type "'.$expectedType.'", but got "'.$actualType.'" for property access on line '.$node->getLine().'.');
        }

        $nodes = \PHPParser_Finder::create($ast)->find('Stmt_PropertyProperty[name=foo]');
        $this->assertSame(1, count($nodes), 'There are two properties named "foo".');
        foreach ($nodes as $node) {
            $this->assertTrue($expectedType->equals($actualType = $node->getAttribute('type')), 'Expected type "'.$expectedType.'", but got "'.$actualType.'" for property declartion on line '.$node->getLine().'.');
        }
    }

    public function testSelf()
    {
        $this->analyzeAst('TypeInference/static_and_self.php');
        $class = $this->registry->getClass('Foo');

        $createSelf = $class->getMethod('createSelf');
        $this->assertTrue($class->equals($createSelf->getReturnType()), sprintf('Expected return type "%s", but got "%s" instead.', $class, $createSelf->getReturnType()));
    }

    public function testStatic()
    {
        $this->analyzeAst('TypeInference/static_and_self.php');
        $class = $this->registry->getClass('Foo');

        $createSelf = $class->getMethod('createStatic');
        $this->assertTrue($class->equals($createSelf->getReturnType()), sprintf('Expected return type "%s", but got "%s" instead.', $class, $createSelf->getReturnType()));
    }

    /**
     * @group refined
     * @group refinedProperty
     */
    public function testRefinedProperty()
    {
        $ast = $this->analyzeAst('TypeInference/refined_property.php');
        $this->assertTypedAst($ast, 'refined_property');
    }

    /**
     * @group refined
     * @group refinedProperty
     * @group refinedProperty2
     */
    public function testRefinedProperty2()
    {
        $ast = $this->analyzeAst('TypeInference/refined_property_2.php');
        $this->assertTypedAst($ast, 'refined_property_2');
    }

    /**
     * @group refined
     * @group refinedStaticProperty
     */
    public function testRefinedStaticProperty()
    {
        $ast = $this->analyzeAst('TypeInference/refined_static_property.php');
        $this->assertTypedAst($ast, 'refined_static_property', true);
    }

    /**
     * @group refined
     * @group refinedVariable
     */
    public function testRefinedVariable()
    {
        $ast = $this->analyzeAst('TypeInference/refined_variable.php');
        $this->assertTypedAst($ast, 'refined_variable');
    }

    /**
     * @group refinedVariable2
     */
    public function testRefinedVariable2()
    {
        $ast = $this->analyzeAst('TypeInference/refined_variable_2.php');
        $this->assertTypedAst($ast, 'refined_variable_2');
    }

    /**
     * @group instanceOf
     */
    public function testInstanceOfInBooleanAnd()
    {
        $ast = $this->analyzeAst('TypeInference/instanceof_in_boolean_and.php');
        $this->assertTypedAst($ast, 'instanceof_in_boolean_and');
    }

    public function getPropertyTests()
    {
        return array(array('properties'), array('constructor_injection'));
    }

    public function testTypeInferenceDoesNotGoIntoInfiniteLoop()
    {
        $this->analyzeAst('TypeInference/infinite_loop.php');
    }

    public function testGlobalConstants()
    {
        $this->analyzeAst('TypeInference/global_constants.php');

        $constants = $this->registry->getConstants();
        $this->assertEquals(3, count($constants));

        $this->assertTrue(isset($constants['foo']), '"foo" constant exists.');
        $this->assertNotNull($type = $constants['foo']->getPhpType(), 'PhpType is not null.');
        $this->assertTrue($type->equals($this->registry->getNativeType('string')), '"'.$type.'" is a string.');

        $this->assertTrue(isset($constants['bar']));
        $this->assertNotNull($type = $constants['bar']->getPhpType());
        $this->assertTrue($type->equals($this->registry->getNativeType('integer')));

        $this->assertTrue(isset($constants['baz']));
        $this->assertNotNull($type = $constants['baz']->getPhpType());
        $this->assertTrue($type->equals($this->registry->getNativeType('double')));
    }

    /**
     * @group arrayPop
     */
    public function testArrayPop()
    {
        $this->registry->registerFunction($arrayPop = new GlobalFunction('array_pop'));
        $arrayPop->setReturnType($this->registry->getNativeType('all'));

        $ast = $this->analyzeAst('TypeInference/array_pop.php');
        $this->assertTypedAst($ast, 'array_pop');
    }

    /**
     * @group foo
     */
    public function testFoo()
    {
        $this->analyzeAst('TypeInference/foo.php');
    }

    public function testInheritedDocComment()
    {
        $this->analyzeAst('TypeInference/inherited_doc_comment.php');

        $d = $this->registry->getClass('D');
        $toString = function($param) {
            return (string) $param->getPhpType();
        };

        $a = $d->getMethod('a');
        $this->assertEquals('boolean', (string) $a->getReturnType());
        $this->assertEquals(array('string', 'integer'), array_map($toString, $a->getParameters()->getValues()));

        $c = $d->getMethod('c');
        $this->assertEquals('string', (string) $c->getReturnType());
        $this->assertEquals(array('string'), array_map($toString, $c->getParameters()->getValues()));

        $d = $d->getMethod('d');
        $this->assertEquals('?', (string) $d->getReturnType());
    }

    /**
     * @group definingExpr
     */
    public function testDefiningExprIsCheckedForVariables()
    {
        $this->registry->registerFunction($function = new GlobalFunction('is_array'));
        $function->setReturnType($this->registry->getNativeType('boolean'));

        $ast = $this->analyzeAst('TypeInference/defining_expr_for_variable.php');
        $this->assertTypedAst($ast, 'defining_expr_for_variable');
    }

    /**
     * @group constant
     */
    public function testConstantAsDefault()
    {
        $ast = $this->analyzeAst('TypeInference/constant_as_default.php');
        $this->assertTypedAst($ast, 'constant_as_default');
    }

    /**
     * @group pcre
     */
    public function testPregReplace()
    {
        $ast = $this->analyzeAst('TypeInference/preg_replace.php');
        $this->assertTypedAst($ast, 'preg_replace');
    }

    /**
     * @group late-binding
     */
    public function testLateBinding()
    {
        $ast = $this->analyzeAst('TypeInference/late_binding_types.php');
        $this->assertTypedAst($ast, 'late_binding_types', true);
    }

    /**
     * @group late-binding
     */
    public function testLateBinding2()
    {
        $ast = $this->analyzeAst('TypeInference/late_binding_types2.php');
        $this->assertTypedAst($ast, 'late_binding_types2', true);
    }

    /**
     * @group late-binding
     */
    public function testLateBinding3()
    {
        $ast = $this->analyzeAst('TypeInference/late_binding_types3.php');
        $this->assertTypedAst($ast, 'late_binding_types3', true);
    }

    /**
     * @group foreach
     */
    public function testForeachWithObject()
    {
        $ast = $this->analyzeAst('TypeInference/foreach_with_object.php');
        $this->assertTypedAst($ast, 'foreach_with_object', true);
    }

    /**
     * @dataProvider getAnnotatedFiles
     * @group annotation-code
     */
    public function testAnnotationCode($file)
    {
        $ast = $this->analyzeAst('TypeInference/annotated-code/'.$file);
        $this->assertAnnotatedAst($ast);
    }

    public function getAnnotatedFiles()
    {
        $tests = array();

        foreach (Finder::create()->in(__DIR__.'/Fixture/TypeInference/annotated-code/')->name('*.php')->files() as $file) {
            assert($file instanceof SplFileInfo);
            $tests[] = array($file->getRelativePathname());
        }

        return $tests;
    }

    /**
     * @group deadlock2
     */
    public function testDeadLock()
    {
        $this->analyzeAst('TypeInference/deadlock_test.php');
    }

    /**
     * @group deadlock3
     */
    public function testDeadLock3()
    {
        $this->analyzeAst('TypeInference/run_test.php');
    }

    /**
     * @group deadlock4
     */
    public function testDeadLock4()
    {
        $this->analyzeAst('TypeInference/run_test2.php');
    }

    protected function getAnalyzer()
    {
        return Analyzer::create(TestUtils::createTestEntityManager(), PassConfig::createForTypeScanning());
    }

    private function assertAnnotatedAst(array $ast)
    {
        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor($visitor = new AssertingVisitor($this));
        $traverser->traverse($ast);
    }

    private function assertTypedAst(array $ast, $filename, $withLines = false)
    {
        if ( ! is_file($astFile = __DIR__.'/Fixture/TypeInference/typed-asts/'.$filename.'.ast')) {
            throw new \InvalidArgumentException(sprintf('Could not find file "%s".', $filename));
        }

        $this->assertEquals(self::dumpAst($ast[0], $withLines), file_get_contents($astFile));
    }

    private function assertType($expectedType, $actualType)
    {
        $expectedType = $this->registry->resolveType($expectedType);
        $actualType = $this->registry->resolveType($actualType);

        $this->assertTrue($expectedType->equals($actualType), sprintf('Failed to assert that actual type "%s" equals expected type "%s".', $actualType, $expectedType));
    }

    /**
     * Dumps only the information of the AST that is relevant for determining
     * that all types are fine.
     *
     * @param \PHPParser_Node $node
     * @param boolean $withLines
     * @param integer $level
     * @return string
     */
    private static function dumpAst(\PHPParser_Node $node, $withLines = false, $level = 0)
    {
        $output = '';
        $writeln = function($line, &$buffer = null) use ($level, &$output) {
            if (null === $buffer) {
                $buffer =& $output;
            }

            $buffer .= str_repeat(' ', $level * 4).$line."\n";
        };

        $writeln(json_encode(get_class($node)).':');

        if ($withLines) {
            $writeln('    line: '.$node->getLine());
        }

        $attrs = $node->getAttributes();
        if (!array_key_exists('type', $attrs)) {
            $writeln('    type: undefined');
        } else if (null === $attrs['type']) {
            $writeln('    type: null');
        } else {
            $writeln('    type: '.json_encode((string) $attrs['type']));
        }

        $childrenContent = '';
        foreach ($node as $name => $subNode) {
            if ($subNode instanceof \PHPParser_Node) {
                $writeln('        '.json_encode($name).':', $childrenContent);
                $childrenContent .= self::dumpAst($subNode, $withLines, $level + 3);
            } else if (is_array($subNode)) {
                $childContent = '';
                foreach ($subNode as $k => $aSubNode) {
                    if ($aSubNode instanceof \PHPParser_Node) {
                        $writeln('            '.json_encode($k).':', $childContent);
                        $childContent .= self::dumpAst($aSubNode, $withLines, $level + 4);
                    }
                }

                if (!empty($childContent)) {
                    $writeln('        '.json_encode($name).':', $childrenContent);
                    $childrenContent .= $childContent;
                }

            }
        }

        if (!empty($childrenContent)) {
             $writeln('    children:');
             $output .= $childrenContent;
        }

        return $output;
    }

    public function getRegistry()
    {
        return $this->registry;
    }
}

class AssertingVisitor extends \PHPParser_NodeVisitorAbstract
{
    private $assertions;
    private $curAssertions = 0;
    private $testCase;
    private $registry;

    public function __construct(TypeInferencePassTest $testCase)
    {
        $this->testCase = $testCase;
        $this->registry = $testCase->getRegistry();
        $this->assertions = new \SplStack();
    }

    public function enterNode(\PHPParser_Node $node)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Stmt_Function:
            case $node instanceof \PHPParser_Node_Stmt_ClassMethod:
                $this->assertions->push($this->curAssertions);
                $this->curAssertions = 0;
                break;

            case $node instanceof \PHPParser_Node_Expr_FuncCall:
                if ((null !== $comment = $node->getDocComment()) && preg_match('#@type ([^\s]+)#', $comment, $match)) {
                    $this->verifyType(
                        $this->registry->resolveType($match[1]),
                        $this->registry->getCalledFunctionByNode($node)->getReturnType(),
                        $node->getLine()
                    );
                }
                break;

            case $node instanceof \PHPParser_Node_Expr_ArrayDimFetch:
            case $node instanceof \PHPParser_Node_Expr_PropertyFetch:
            case $node instanceof \PHPParser_Node_Expr_StaticPropertyFetch:
            case $node instanceof \PHPParser_Node_Expr_Variable:
                if (null !== $comment = $node->getDocComment()) {
                    // For some reason, doc comments are inherited for DimFetches. A behavior
                    // that we do not want in this case.
                    if ($node->getAttribute('parent') instanceof \PHPParser_Node_Expr_ArrayDimFetch
                            && $comment === $node->getAttribute('parent')->getDocComment()) {
                        break;
                    }

                    if (preg_match('#@type ([^\s]+)#', $comment, $match)) {
                        $this->verifyType(
                            $this->registry->resolveType($match[1]),
                            $node->getAttribute('type'),
                            $node->getLine()
                        );
                    }

                    if (false !== strpos($comment, '@no-type')) {
                        $this->curAssertions += 1;
                        // We cannot use assertNull here because in case of an error, PHPUnit takes endlessly to dump
                        // whatever was passed as first argument.
                        $this->testCase->assertTrue(null === $node->getAttribute('type'), sprintf('Actual type was "%s", but expected no defined type (no node attribute).', $node->getAttribute('type')));
                    }
                }
                break;
        }
    }

    private function verifyType(\Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType $expectedType, \Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType $actualType = null, $line)
    {
        $this->curAssertions += 1;
        $this->testCase->assertNotNull($actualType, sprintf('Actual type was null (not defined), but expected type "%s" on line %d.', $expectedType, $line));
        $this->testCase->assertTrue($expectedType->equals($actualType), sprintf('Failed to assert that actual type "%s" equals expected type "%s" on line %d.', $actualType, $expectedType, $line));
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Stmt_Function:
            case $node instanceof \PHPParser_Node_Stmt_ClassMethod:
                if (0 === count($node->stmts)) {
                    break;
                }

                if ((null === $comment = $node->getDocComment())
                        || ! preg_match('#@Assertions\(([0-9]+)\)#', $comment, $match)) {
                    $this->testCase->fail(sprintf('Each method, and function must have an @Assertions() annotation, but found none on line %d.', $node->getLine()));
                }

                $this->testCase->assertEquals($match[1], $this->curAssertions, sprintf('The number of expected assertions does not equal the actually performed number of assertions for function/method on line %d.', $node->getLine()));

                break;
        }
    }
}