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

namespace Scrutinizer\Tests\PhpAnalyzer\DataFlow\TypeInference\ReferenceInterpreter;

use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\LinkedFlowScope;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\ReverseInterpreter\SemanticReverseAbstractInterpreter;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\InterfaceC;

class SemanticReverseAbstractInterpreterTest extends \PHPUnit_Framework_TestCase
{
    /** @var SemanticReverseAbstractInterpreter */
    private $interpreter;

    /** @var TypeRegistry */
    private $registry;

    /** @var Scope */
    private $functionScope;

    /**
     * if ($a) { }
     *
     * @dataProvider getVariableConditionTests
     * @group variable-condition
     */
    public function testVariableCondition($variableType, $trueType, $falseType)
    {
        $blind = $this->newScope();
        $condition = $this->createVar($blind, 'a', $this->registry->resolveType($variableType));

        // true outcome.
        $informedTrue = $this->interpreter->getPreciserScopeKnowingConditionOutcome($condition, $blind, true);
        $this->assertTypeEquals($trueType, $this->getVarType($informedTrue, 'a'));

        // false outcome.
        $informedFalse = $this->interpreter->getPreciserScopeKnowingConditionOutcome($condition, $blind, false);
        $this->assertTypeEquals($falseType, $this->getVarType($informedFalse, 'a'));
    }

    public function getVariableConditionTests()
    {
        $tests = array();

        $tests[] = array('string|null', 'string', 'string|null');
        $tests[] = array('string|false', 'string', 'string|false');

        return $tests;
    }

    // if (!$a) { }
    public function testNegatedVariableCondition()
    {
        $blind = $this->newScope();
        $a = $this->createVar($blind, 'a', $this->createNullableType('string'));
        $condition = new \PHPParser_Node_Expr_BooleanNot($a);

        // true outcome.
        $informedTrue = $this->interpreter->getPreciserScopeKnowingConditionOutcome($condition, $blind, true);
        $this->assertTypeEquals($this->createNullableType('string'), $this->getVarType($informedTrue, 'a'));

        // false outcome.
        $informedFalse = $this->interpreter->getPreciserScopeKnowingConditionOutcome($condition, $blind, false);
        $this->assertTypeEquals('string', $this->getVarType($informedFalse, 'a'));
    }

    // if ($a = $b) { }
    public function testAssignCondition1()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'Assign',
            $this->createVar($blind, 'a', $this->createNullableType('object')),
            $this->createVar($blind, 'b', $this->createNullableType('object')),
            array('a' => 'object', 'b' => 'object'),
            array('a' => 'null', 'b' => 'null'));
    }

    // if ($a === 56) { }
    public function testSheqCondition1()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'Identical',
            $this->createVar($blind, 'a', $this->createUnionType('string', 'integer')),
            $this->createNumber(56),
            array('a' => 'integer'),
            array('a' => $this->createUnionType(array('string', 'integer'))));
    }

    // if (56 === $a) { }
    public function testSheqCondition2()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'Identical',
            $this->createNumber(56),
            $this->createVar($blind, 'a', $this->createUnionType(array('string', 'integer'))),
            array('a' => 'integer'),
            array('a' => $this->createUnionType(array('string', 'integer'))));
    }

    // if ($b === $a) { }
    public function testSheqCondition3()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'Identical',
            $this->createVar($blind, 'b', $this->createUnionType('string', 'boolean')),
            $this->createVar($blind, 'a', $this->createUnionType('string', 'number')),
            array('b' => 'string', 'a' => 'string'),
            array('b' => $this->createUnionType('string', 'boolean'), 'a' => $this->createUnionType('string', 'number')));
    }

    // if ($a === $b) { }
    public function testSheqCondition4()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'Identical',
            $this->createVar($blind, 'a', $this->createUnionType('string', 'null')),
            $this->createVar($blind, 'b', 'null'),
            array('a' => 'null', 'b' => 'null'),
            array('a' => 'string', 'b' => 'null'));
    }

    public function testSheqCondition5() // testSheqCondition6
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'Identical',
            $this->createVar($blind, 'a', $this->createUnionType('string', 'null')),
            $this->createVar($blind, 'b', $this->createUnionType('number', 'null')),
            array('a' => 'null', 'b' => 'null'),
            array('a' => $this->createUnionType('string', 'null'), 'b' => $this->createUnionType('number', 'null')));
    }

    public function testShneCondition1()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'NotIdentical',
            $this->createVar($blind, 'a', $this->createUnionType('string', 'integer')),
            $this->createNumber(56),
            array('a' => $this->createUnionType('string', 'integer')),
            array('a' => 'integer'));
    }

    public function testShneCondition2()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'NotIdentical',
            $this->createNumber(56),
            $this->createVar($blind, 'a', $this->createUnionType('string', 'integer')),
            array('a' => $this->createUnionType('string', 'integer')),
            array('a' => 'integer'));
    }

    public function testShneCondition3()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'NotIdentical',
            $this->createVar($blind, 'b', $this->createUnionType('string', 'boolean')),
            $this->createVar($blind, 'a', $this->createUnionType('string', 'integer')),
            array('b' => $this->createUnionType('string', 'boolean'), 'a' => $this->createUnionType('string', 'integer')),
            array('a' => 'string', 'b' => 'string'));
    }

    public function testShneCondition4()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'NotIdentical',
            $this->createVar($blind, 'a', 'string|null'),
            $this->createVar($blind, 'b', 'null'),
            array('a' => 'string', 'b' => 'null'),
            array('a' => 'null', 'b' => 'null'));
    }

    public function testShneCondition5() // testShneCondition6
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'NotIdentical',
            $this->createVar($blind, 'a', 'string|null'),
            $this->createVar($blind, 'b', 'integer|null'),
            array('a' => 'string|null', 'b' => 'integer|null'),
            array('a' => 'null', 'b' => 'null'));
    }

    // $a = true|false|null; if ($a == null) { /** $a = false|null */ } else { /* $a = true */ }
    public function testEqCondition1()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'Equal',
            $this->createVar($blind, 'a', 'boolean|null'),
            $this->createNull(),
            array('a' => 'boolean|null'),
            array('a' => 'boolean'));
    }

    public function testEqCondition2()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'NotEqual',
            $this->createNull(),
            $this->createVar($blind, 'a', 'boolean|null'),
            array('a' => 'boolean'),
            array('a' => 'boolean|null'));
    }

    // $a = 0|0.54|null; if ($a == null) { /** $a = null|0; */ } else { /* $a = 0.54 */ }
    public function testEqCondition3()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'Equal',
            $this->createVar($blind, 'a', 'null|number'),
            $this->createNull(),
            array('a' => 'null|number'),
            array('a' => 'number'));
    }

    public function testEqCondition4()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'Equal',
            $this->createVar($blind, 'a', 'null'),
            $this->createVar($blind, 'b', 'null'),
            array('a' => 'null', 'b' => 'null'),
            array('a' => 'null', 'b' => 'null'));
    }

    /**
     * Tests LE, LT, GE, or GT.
     *
     * @dataProvider getInequalityTests1
     */
    public function testInequalitiesCondition1($op, $leftType, $rightType, $trueOutcome, $falseOutcome)
    {
        $blind = $this->newScope();
        $rightSide = is_int($rightType) ? $this->createNumber($rightType) : $this->createVar($blind, 'b', $rightType);

        $this->doTestBinop($blind, $op,
            $this->createVar($blind, 'a', $leftType),
            $rightSide,
            array('a' => $trueOutcome),
            array('a' => $falseOutcome));
    }

    public function getInequalityTests1()
    {
        $tests = array();

        // Values: <Operation> <Left Types> <Right Types> <Types on true> <Types on false>
        $tests[] = array('SmallerOrEqual', 'string|null', 'integer', 'string|null', 'string');
        $tests[] = array('SmallerOrEqual', 'string|null', 'double', 'string|null', 'string');
        $tests[] = array('Smaller', 'string|null', 'integer', 'string|null', 'string|null');
        $tests[] = array('Smaller', 'string|null', 'double', 'string|null', 'string|null');
        $tests[] = array('Greater', 'string|null', 'integer', 'string', 'string|null');
        $tests[] = array('Greater', 'string|null', 'double', 'string', 'string|null');
        $tests[] = array('GreaterOrEqual', 'string|null', 'integer', 'string|null', 'string|null');
        $tests[] = array('GreaterOrEqual', 'string|null', 'double', 'string|null', 'string|null');

        return $tests;
    }

    /**
     * @dataProvider getInequalityTests2
     */
    public function testInequalititesCondition2($op, $leftType, $rightType, $trueOutcome, $falseOutcome)
    {
        $blind = $this->newScope();
        $leftSide = is_int($leftType) ? $this->createNumber($leftType) : $this->createVar($blind, 'b', $leftType);

        $this->doTestBinop($blind, $op,
                $leftSide,
                $this->createVar($blind, 'a', $rightType),
                array('a' => $trueOutcome),
                array('a' => $falseOutcome));
    }

    public function getInequalityTests2()
    {
        $tests = array();

        // Values: <Operation> <Left Types> <Right Types> <Types on true> <Types on false>
        $tests[] = array('GreaterOrEqual', 'integer', 'string|null', 'string|null', 'string');
        $tests[] = array('GreaterOrEqual', 'double', 'string|null', 'string|null', 'string');
        $tests[] = array('Smaller', 'integer', 'string|null', 'string', 'string|null');
        $tests[] = array('Smaller', 'double', 'string|null', 'string', 'string|null');
        $tests[] = array('SmallerOrEqual', 'integer', 'string|null', 'string|null', 'string|null');
        $tests[] = array('SmallerOrEqual', 'integer', 'string|null', 'string|null', 'string|null');
        $tests[] = array('Greater', 'integer', 'string|null', 'string|null', 'string|null');
        $tests[] = array('Greater', 'double', 'string|null', 'string|null', 'string|null');

        return $tests;
    }

    public function testBooleanAnd()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'BooleanAnd',
            $this->createVar($blind, 'b', 'string|null'),
            $this->createVar($blind, 'a', 'number'),
            array('a' => 'number', 'b' => 'string'),
            array('a' => 'number', 'b' => 'string|null'));
    }

    public function testLogicalAnd()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind, 'LogicalAnd',
            $this->createVar($blind, 'b', 'string|null'),
            $this->createVar($blind, 'a', 'number'),
            array('b' => 'string', 'a' => 'number'),
            array('a' => 'number', 'b' => 'string|null'));
    }

    public function testGettype1()
    {
        $blind = $this->newScope();
        $this->doTestTypeFunction($blind, 'gettype',
            $this->createVar($blind, 'a', 'object|number'),
            'object',
            array('a' => 'object'),
            array('a' => 'number'));
    }

    /**
     * @group null
     */
    public function testIsNull()
    {
        $blind = $this->newScope();
        $this->doTestTypeFunction($blind, 'is_null',
            $this->createVar($blind, 'a', $this->createNullableType('object')),
            true,
            array('a' => 'null'),
            array('a' => 'object'));
    }

    /**
     * @dataProvider getIsIntegerAliases
     */
    public function testIsInteger($alias)
    {
        $blind = $this->newScope();
        $this->doTestTypeFunction($blind, $alias,
            $this->createVar($blind, 'a', 'all'),
            true,
            array('a' => 'integer'),
            array('a' => 'object|double|string|null|array|boolean'));
    }

    public function getIsIntegerAliases()
    {
        return array(array('is_integer'), array('is_int'), array('is_long'));
    }

    /**
     * @dataProvider getIsDoubleAliases
     */
    public function testIsDouble($alias)
    {
        $blind = $this->newScope();
        $this->doTestTypeFunction($blind, $alias,
            $this->createVar($blind, 'a', 'number|string'),
            true,
            array('a' => 'double'),
            array('a' => 'integer|string'));
    }

    /**
     * @group is_callable
     * @dataProvider getCallableTests
     */
    public function testIsCallable($blindType, $trueType, $falseType)
    {
        $blind = $this->newScope();
        $this->doTestTypeFunction($blind, 'is_callable',
            $this->createVar($blind, 'a', $blindType),
            true,
            array('a' => $trueType),
            array('a' => $falseType));
    }

    public function getCallableTests()
    {
        // Blind Type | True Type | False Type
        $tests = array();
        $tests[] = array('unknown', 'callable', 'unknown');
        $tests[] = array('all', 'callable', 'all');
        $tests[] = array('boolean|callable', 'callable', 'boolean');
        $tests[] = array('callable', 'callable', 'none');
        $tests[] = array('string', 'string', 'string'); // The false scope is also a string as the function simply might not exist.
        $tests[] = array('string|array', 'string|array', 'string|array'); // Same as above.
        $tests[] = array('string|boolean', 'string', 'string|boolean'); // Same as above.
        $tests[] = array('integer|boolean|string|array', 'string|array', 'string|array|integer|boolean'); // Same.
        $tests[] = array('object<Foo>', 'object<Foo>', 'object<Foo>'); // Same.

        return $tests;
    }

    /**
     * @group non-refinable
     */
    public function testArrayExprNonRefinable()
    {
        $n = new \PHPParser_Node_Expr_Array();
        $n->setAttribute('type', $this->registry->getNativeType('array'));

        $this->doTestTypeFunction($this->newScope(),
                'is_callable',
                $n,
                true,
                array(),
                array());
    }

    /**
     * @group is_array
     */
    public function testIsArray()
    {
        $blind = $this->newScope();
        $this->doTestTypeFunction($blind, 'is_array',
            $this->createVar($blind, 'a', 'string|array'),
            true,
            array('a' => 'array'),
            array('a' => 'string'));
    }

    /**
     * @group is_array
     */
    public function testIsArrayWhenUnknown()
    {
        $blind = $this->newScope();
        $this->doTestTypeFunction($blind, 'is_array',
            $this->createVar($blind, 'a', 'unknown'),
            true,
            array('a' => 'array'),
            array('a' => 'unknown'));
    }

    /**
     * @group is_array
     */
    public function testNegatedIsArrayWhenUnknown()
    {
        $blind = $this->newScope();
        $this->doTestTypeFunction($blind, 'is_array',
            $this->createVar($blind, 'a', 'unknown'),
            false,
            array('a' => 'unknown'),
            array('a' => 'array'));
    }

    /**
     * @group is_array
     */
    public function testIsArrayWhenOnlyType()
    {
        $blind = $this->newScope();
        $this->doTestTypeFunction($blind, 'is_array',
            $this->createVar($blind, 'a', 'array'),
            true,
            array('a' => 'array'),
            array('a' => 'none'));
    }

    public function getIsDoubleAliases()
    {
        return array(array('is_double'), array('is_float'), array('is_real'));
    }

    public function testIsString()
    {
        $blind = $this->newScope();
        $this->doTestTypeFunction($blind, 'is_string',
            $this->createVar($blind, 'a', 'null|string'),
            true,
            array('a' => 'string'),
            array('a' => 'null'));
    }

    /**
     * @group is_object
     * @dataProvider getIsObjectTests
     */
    public function testIsObject($assumedType, $trueType, $falseType)
    {
        $blind = $this->newScope();
        $this->doTestTypeFunction($blind, 'is_object',
            $this->createVar($blind, 'a', $assumedType),
            true,
            array('a' => $trueType),
            array('a' => $falseType));
    }

    public function getIsObjectTests()
    {
        $tests = array();

        // Assumed Type,   True Type,   False Type
        $tests[] = array('object<Foo>|null', 'object<Foo>', 'null');
        $tests[] = array('string', 'none', 'string');
        $tests[] = array('all', 'object', 'null|integer|double|resource|array|string|boolean');
        $tests[] = array('string|double', 'none', 'string|double');

        return $tests;
    }

    /**
     * @group get_class
     */
    public function testGetClass()
    {
        $blind = $this->newScope();
        $this->doTestGetClassFunction($blind,
            $this->createVar($blind, 'x', 'object'),
            'Foo\Bar',
            array('x' => 'object<Foo\Bar>'),
            array('x' => 'object'));
    }

    /**
     * @group get_class
     */
    public function testGetClass2()
    {
        $blind = $this->newScope();
        $this->doTestGetClassFunction($blind,
            $this->createVar($blind, 'x', 'object<Foo>'),
            'Foo',
            array('x' => 'object<Foo>'),
            array('x' => 'none'));
    }

    /**
     * @group get_class
     */
    public function testGetClass3()
    {
        $blind = $this->newScope();
        $this->doTestGetClassFunction($blind,
            $this->createVar($blind, 'x', 'integer|object'),
            'Foo',
            array('x' => 'object<Foo>'),
            array('x' => 'integer|object'));
    }

    /**
     * @group get_class
     */
    public function testGetClass4()
    {
        $blind = $this->newScope();
        $this->doTestGetClassFunction($blind,
            $this->createVar($blind, 'x', 'integer'),
            'Foo',
            array('x' => 'none'),
            array('x' => 'integer'));
    }

    /**
     * @group get_class
     */
    public function testGetClass5()
    {
        $blind = $this->newScope();
        $this->doTestGetClassFunction($blind,
            $this->createVar($blind, 'x', 'integer|double'),
            'Foo',
            array('x' => 'none'),
            array('x' => 'integer|double'));
    }

    /**
     * @group instanceOf
     * @dataProvider getInstanceOfTests
     */
    public function testInstanceOf1($assumedType, $className, $trueType, $falseType)
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind,
                           'Instanceof',
                           $this->createVar($blind, 'x', $assumedType),
                           $this->createClass($className),
                           array('x' => $trueType),
                           array('x' => $falseType));
    }

    /**
     * @group instanceOf
     * @dataProvider getInstanceOfTests
     */
    public function testInstanceOf2($assumedType, $className, $trueType, $falseType)
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind,
                           'Instanceof',
                           $this->createVar($blind, 'x', $assumedType),
                           $this->createInterface($className),
                           array('x' => $trueType),
                           array('x' => $falseType));
    }

    /**
     * @group instanceOf
     * @dataProvider getInstanceOfTests
     */
    public function testInstanceOf3($assumedType, $className, $trueType, $falseType)
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind,
                           'Instanceof',
                           $this->createVar($blind, 'x', $assumedType),
                           $this->createNamed($className),
                           array('x' => $trueType),
                           array('x' => $falseType));
    }

    /**
     * If the class is a variable, e.g. ``if ($x instanceof $y)`` and the condition
     * evaluates to true, then we can infer about $x that it must be an object.
     *
     * @group instanceOf
     */
    public function testInstanceOf4()
    {
        $blind = $this->newScope();
        $this->doTestBinop($blind,
                           'Instanceof',
                           $this->createVar($blind, 'x', 'string|object<Foo>|null'),
                           $this->createVar($blind, 'y', 'string'),
                           array('x' => 'object<Foo>'),
                           array('x' => 'string|object<Foo>|null'));
    }

    /**
     * @group foobar
     */
    public function testInstanceOf5()
    {
        $this->setUpInheritance(array('Foo' => 'BarInterface'));
        $blind = $this->newScope();
        $this->doTestBinop($blind,
                           'Instanceof',
                           $this->createVar($blind, 'x', 'object<BarInterface>'),
                           $this->createClass($this->registry->getClass('Foo')),
                           array('x' => 'object<Foo>'),
                           array('x' => 'object<BarInterface>'));
    }

    public function getInstanceOfTests()
    {
        $tests = array();

        $tests[] = array('object', 'Foo', 'object<Foo>', 'object');
        $tests[] = array('unknown', 'Foo', 'object<Foo>', 'unknown');
        $tests[] = array('string', 'Foo', 'none', 'string');
        $tests[] = array('object<Bar>', 'Foo', 'none', 'object<Bar>');
        $tests[] = array('string|object<Foo>', 'Foo', 'object<Foo>', 'string');
        $tests[] = array('object<BarInterface>', 'Foo', 'none', 'object<BarInterface>');

        return $tests;
    }

    /**
     * @group assert
     */
    public function testAssert()
    {
        $this->setUpInheritance(array('B' => 'A'));

        $blind = $this->newScope();
        $op = new \PHPParser_Node_Expr_Instanceof(
            $this->createVar($blind, 'x', 'object<A>'), $name = new \PHPParser_Node_Name_FullyQualified(array('B')));
        $name->setAttribute('type', $this->registry->resolveType('object<B>'));

        $this->doTestTypeFunction($blind, 'assert',
            $op,
            true,
            array('x' => 'object<B>'),
            array('x' => 'object<A>'));
    }

    protected function setUp()
    {
        $this->registry = new TypeRegistry();
        $this->interpreter = new SemanticReverseAbstractInterpreter($this->registry);
    }

    private function doTestGetClassFunction(LinkedFlowScope $blind, $arg, $outcome, array $trueOutcome, array $falseOutcome)
    {
        $function = new \PHPParser_Node_Expr_FuncCall(new \PHPParser_Node_Name(array('get_class')));
        $function->args[] = new \PHPParser_Node_Arg($arg);
        $function->setAttribute('type', $this->registry->getNativeType('string'));

        $this->doTestBinop($blind, 'Equal', $function, $this->resolveOutcome($outcome), $trueOutcome, $falseOutcome);
    }

    private function doTestTypeFunction(LinkedFlowScope $blind, $name, $arg, $outcome, array $trueOutcome, array $falseOutcome)
    {
        $function = new \PHPParser_Node_Expr_FuncCall(new \PHPParser_Node_Name(array($name)));
        $function->args[] = new \PHPParser_Node_Arg($arg);

        if ('gettype' !== $name) {
            $function->setAttribute('type', $this->registry->getNativeType('boolean'));
        }

        $this->doTestBinop($blind, 'Equal', $function, $this->resolveOutcome($outcome), $trueOutcome, $falseOutcome);
    }

    private function doTestBinop(LinkedFlowScope $blind, $binop, \PHPParser_Node $left, \PHPParser_Node $right, array $trueOutcome, array $falseOutcome)
    {
        $opClass = 'PHPParser_Node_Expr_'.$binop;
        $condition = new $opClass($left, $right);

        // true outcome.
        $informedTrue = $this->interpreter->getPreciserScopeKnowingConditionOutcome($condition, $blind, true);
        foreach ($trueOutcome as $name => $type) {
            $type = $this->registry->resolveType($type);
            $varType = $this->getVarType($informedTrue, $name);
            $this->assertTrue($type->equals($varType), 'Expected true outcome of type "'.$type.'" for variable "'.$name.'" does not equal actual type "'.$varType.'".');
        }

        // false outcome.
        $informedFalse = $this->interpreter->getPreciserScopeKnowingConditionOutcome($condition, $blind, false);
        foreach ($falseOutcome as $name => $type) {
            $type = $this->registry->resolveType($type);
            $varType = $this->getVarType($informedFalse, $name);
            $this->assertTrue($type->equals($varType), 'Expected false outcome of type "'.$type.'" for variable "'.$name.'" does not equal actual type "'.$varType.'".');
        }
    }

    private function resolveOutcome($outcome)
    {
        if ($outcome instanceof \PHPParser_Node) {
            return $outcome;
        }

        if (is_string($outcome)) {
            return new \PHPParser_Node_Scalar_String($outcome);
        }

        if (is_bool($outcome)) {
            $outcome = new \PHPParser_Node_Expr_ConstFetch(new \PHPParser_Node_Name(array($outcome ? 'true' : 'false')));
            $outcome->setAttribute('type', $this->registry->getNativeType('boolean'));

            return $outcome;
        }

        throw new \InvalidArgumentException('$outcome must be a string, boolean, or an instance of PHPParser_Node.');
    }

    private function assertTypeEquals($a, $b)
    {
        $a = $this->registry->resolveType($a);
        $b = $this->registry->resolveType($b);
        $this->assertTrue($a->equals($b), 'Types '.$a.' and '.$b.' are not equivalent.');
    }

    private function createNumber($number)
    {
        if (is_int($number)) {
            $n = new \PHPParser_Node_Scalar_LNumber($number);
            $n->setAttribute('type', $this->registry->getNativeType('integer'));
        } else if (is_double($number)) {
            $n = new \PHPParser_Node_Scalar_DNumber($number);
            $n->setAttribute('type', $this->registry->getNativeType('double'));
        } else {
            throw new \InvalidArgumentException('Invalid number: '.var_export($number, true));
        }

        return $n;
    }

    private function createNull()
    {
        $n = new \PHPParser_Node_Expr_ConstFetch(new \PHPParser_Node_Name(array('null')));
        $n->setAttribute('type', $this->registry->resolveType('null'));

        return $n;
    }

    private function createClass($class)
    {
        $n = new \PHPParser_Node_Name_FullyQualified(explode("\\", $class));

        if (is_string($class)) {
            $class = new \Scrutinizer\PhpAnalyzer\Model\Clazz($class);
            $class->setNormalized(true);
            $this->registry->registerClass($class);
        }

        $n->setAttribute('type', $class);

        return $n;
    }

    private function createInterface($name)
    {
        $n = new \PHPParser_Node_Name_FullyQualified(explode("\\", $name));

        $interface = new \Scrutinizer\PhpAnalyzer\Model\InterfaceC($name);
        $interface->setNormalized(true);
        $this->registry->registerClass($interface);
        $n->setAttribute('type', $interface);

        return $n;
    }

    private function createNamed($name)
    {
        $n = new \PHPParser_Node_Name_FullyQualified(explode("\\", $name));

        $type = $this->registry->getClassOrCreate($name);
        $n->setAttribute('type', $type);

        return $n;
    }

    private function createNullableType($type)
    {
        $type = $this->registry->resolveType($type);

        return $this->registry->createNullableType($type);
    }

    private function createUnionType($types)
    {
        if (!is_array($types)) {
            $types = func_get_args();
        }

        return $this->registry->createUnionType($types);
    }

    private function getVarType(LinkedFlowScope $scope, $name)
    {
        $slot = $scope->getSlot($name);
        $this->assertNotNull($slot, 'Scope does not contain slot named "'.$name.'".');

        return $slot->getType();
    }

    private function createVar(LinkedFlowScope $scope, $name, $type)
    {
        $type = $this->registry->resolveType($type);
        $this->functionScope->declareVar($name)
            ->setNameNode($n = new \PHPParser_Node_Expr_Variable($name));
        $scope->inferSlotType($name, $type);
        $n->setAttribute('type', $type);

        return $n;
    }

    private function setUpInheritance(array $extends = array())
    {
        if ( ! $extends) {
            return;
        }

        foreach ($extends as $class => $extendedClass) {
            if ($this->registry->hasClass($class)) {
                continue;
            }

            $this->setUpClass($class, $extends);
        }
    }

    private function setUpClass($class, array $extends)
    {
        $class = new Clazz($class);
        if (isset($extends[$class->getName()])) {
            $parentClass = $this->setUpClass($extends[$class->getName()], $extends);
            $class->setSuperClasses(array_merge(array($parentClass->getName()), $parentClass->getSuperClasses()));
            $class->setSuperClass($parentClass->getName());
        }

        $class->setNormalized(true);
        $this->registry->registerClass($class);

        return $class;
    }

    private function newScope()
    {
        $globalScope = new Scope($this->getMock('PHPParser_Node'));
        $this->functionScope = new Scope($this->getMock('PHPParser_Node'), $globalScope);

        return LinkedFlowScope::createLatticeElement($this->functionScope);
    }
}