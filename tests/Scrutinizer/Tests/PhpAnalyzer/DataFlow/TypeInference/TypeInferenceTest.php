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

namespace Scrutinizer\Tests\PhpAnalyzer\DataFlow\TypeInference;

use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowAnalysis;
use Scrutinizer\PhpAnalyzer\DataFlow\DataFlowAnalysis;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\FunctionInterpreter\ArrayFunctionInterpreter;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\LinkedFlowScope;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\ReverseInterpreter\SemanticReverseAbstractInterpreter;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypeInference;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypedScopeCreator;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\Method;
use JMS\PhpManipulator\PhpParser\NormalizingNodeVisitor;

class TypeInferenceTest extends \PHPUnit_Framework_TestCase
{
    private $assumptions = array();
    private $registry;
    private $parser;
    private $returnScope;
    private $astGraph;

    public function testAssumption()
    {
        $this->assuming('x', 'integer');
        $this->inMethod('');
        $this->verify('x', 'integer');
    }

    public function testVar()
    {
        $this->inMethod('$x = 1;');
        $this->verify('x', 'integer');
    }

    public function testAssignment()
    {
        $this->assuming('x', 'object');
        $this->inMethod('$x = 0.4;');
        $this->verify('x', 'double');
    }

    public function testIf1()
    {
        $this->assuming('x', $this->createNullableType('object'));
        $this->inMethod('$y = new $foo; if ($x) { $y = $x; }');
        $this->verifySubtypeOf('y', 'object');
    }

    public function testIf2()
    {
        $this->assuming('x', $this->createNullableType('object'));
        $this->inMethod('$y = $x; if ($x) { $y = $x; } else { $y = new $foo; }');
        $this->verifySubtypeOf('y', 'object');
    }

    public function testIf3()
    {
        $this->assuming('x', $this->createNullableType('object'));
        $this->inMethod('$y = 1; if ($x) { $y = $x; }');
        $this->verify('y', $this->registry->createUnionType(array('object', 'integer')));
    }

    public function testReturn1()
    {
        $this->assuming('x', $this->createNullableType('object'));
        $this->inMethod('if ($x) { return $x; } $x = new $foo; return $x;');
        $this->verify('x', 'object');
    }

    public function testReturn2()
    {
        $this->assuming('x', $this->createNullableType('integer'));
        $this->inMethod('if (!$x) { $x = 0; } return $x;');
        $this->verify('x', 'integer');
    }

    public function testWhile1()
    {
        $this->assuming('x', $this->createNullableType('integer'));
        $this->inMethod('while (!$x) { if (null === $x) { $x = 0; } else { $x = 1; } }');
        $this->verify('x', 'integer');
    }

    public function testWhile2()
    {
        $this->assuming('x', $this->createNullableType('integer'));
        $this->inMethod('while (!$x) { $x = array(); }');
        $this->verifySubtypeOf('x', $this->registry->createUnionType(array('integer', 'array')));
    }

    public function testDo()
    {
        $this->assuming('x', $this->createNullableType('object'));
        $this->inMethod('do { $x = 1; } while (!$x);');
        $this->verify('x', 'integer');
    }

    public function testFor1()
    {
        $this->assuming('y', 'integer');
        $this->inMethod('$x = $i = null; for ($i=$y; !$i; $i=1) { $x = 1; }');
        $this->verify('x', $this->createNullableType('integer'));
        $this->verify('i', 'integer');
    }

    public function testFor2() // testFor4
    {
        $this->assuming('x', $this->createNullableType('object'));
        $this->inMethod('$y = array(); if ($x) { for ($i=0; $i<10; $i++) { break; } $y = $x; }');
        $this->verifySubtypeOf('y', 'object|array');
    }

    /**
     * @group foreach
     */
    public function testForeach1() // testFor3
    {
        $this->assuming('y', 'object');
        $this->inMethod('$x = $i = null; foreach ($y as $i => $stuff) { $x = 1; }');
        $this->verify('x', $this->createNullableType('integer'));
        $this->verify('i', $this->registry->createUnionType(array('null', 'string', 'integer')));
    }

    /**
     * @group foreach
     */
    public function testForeach2()
    {
        $this->assuming('y', 'array<object<Foo>>');
        $this->inMethod('$x = $i = null; foreach ($y as $i => $x);');
        $this->verify('x', $this->createNullableType('object<Foo>'));
        $this->verify('i', $this->createNullableType('integer'));
    }

    /**
     * @group foreach
     */
    public function testForeach3()
    {
        $this->registry->registerClass($foo = new Clazz('Foo'));
        $foo->setTypeRegistry($this->registry);
        $foo->addImplementedInterface('Iterator');
        $foo->addMethod($current = new Method('current'));
        $current->setReturnType($this->registry->getClassOrCreate('Bar'));

        $this->assuming('y', 'object<Foo>');
        $this->inMethod('$x = $i = null; foreach ($y as $i => $x);');
        $this->verify('x', $this->createNullableType('object<Bar>'));
        $this->verify('i', $this->createNullableType('integer|string'));
    }

    public function testSwitch1()
    {
        $this->assuming('x', 'integer');
        $this->inMethod('
            $y = null;
            switch ($x) {
                case 1: $y = 1; break;
                case 2: $y = array(); // fall through
                case 3: $y = array(); // fall through
                default: $y = 0;
            }
        ');
        $this->verify('y', 'integer');
    }

    public function testSwitch2()
    {
        $this->assuming('x', 'all');
        $this->inMethod('
            $y = null;
            switch (gettype($x)) {
                case "string":
                    $y = $x;
                    return;

                default:
                    $y = "a";
            }
        ');
        $this->verify('y', 'string');
    }

    public function testSwitch3()
    {
        $this->assuming('x', $this->createNullableType($this->registry->createUnionType(array('integer', 'string'))));
        $this->inMethod('
            switch (gettype($x)) {
                case "string":
                    $y = 1; $z = null;
                    return;

                case "integer":
                    $y = $x; $z = null;
                    return;

                default:
                    $y = 1; $z = $x;
            }
        ');
        $this->verify('y', 'integer');
        $this->verify('z', 'null');
    }

    public function testSwitch4()
    {
        $this->assuming('x', 'all');
        $this->inMethod('$y = null;
            switch(gettype($x)) {
                case "string":
                case "integer":
                    $y = $x;
                    return;
                default:
                    $y = 1;
            }
        ');
        $this->verify('y', 'integer|string');
    }

    public function testTernary()
    {
        $this->assuming('x', $this->createNullableType('object'));
        $this->inMethod('$y = $x ? $x : array();');
        $this->verifySubtypeOf('y', 'object|array');
    }

    public function testThrow()
    {
        $this->assuming('x', $this->createNullableType('number'));
        $this->inMethod('$y = 1;
            if (null === $x) { throw new Exception("x is null"); }
            $y = $x;');
        $this->verify('y', 'number');
    }

    public function testTry1() // testTry3
    {
        $this->assuming('x', 'double');
        $this->inMethod('$y = null; try { $y = $x; } catch (Exception $e) { }');
        $this->verify('y', 'double');
    }

    public function testCatch1()
    {
        $class = $this->createClass('Exception');
        $this->inMethod('$y = null; try { foo(); } catch (Exception $ex) { $y = $ex; }');
        $this->verify('y', $this->createNullableType($class));
    }

    public function testCatch2()
    {
        $class = $this->createClass('Exception');
        $this->inMethod('$y = null; $e = 3; try { foo(); } catch (Exception $e) { $y = $e; }');
        $this->verify('y', $this->createNullableType($class));
    }

    public function testClosure()
    {
        $this->assuming('x', 'null');
        $this->inMethod('$x = function() { };');
        $this->verify('x', 'object<Closure>');
    }

    public function testUnknownType1()
    {
        $this->inMethod('$y = 3; $y = $x;');
        $this->verify('y', 'unknown');
    }

    public function testUnknownType2()
    {
        $this->assuming('x', 'array');
        $this->inMethod('$y = 5; $y = $x[0];');
        $this->verify('y', 'unknown');
    }

    public function testInfiniteLoop1()
    {
        $this->assuming('x', $this->createNullableType('array'));
        $this->inMethod('$x = array(); while ($x !== null) { $x = array(); }');
    }

    public function testInifiniteLoop2()
    {
        $this->assuming('x', $this->createNullableType('array'));
        $this->inMethod('$x = array(); do { $x = null; } while ($x === null);');
    }

    public function testJoin1()
    {
        $this->assuming('x', 'boolean');
        $this->assuming('unknownOrNull', 'unknown|null');
        $this->inMethod('if ($x) { $y = $unknownOrNull; } else $y = null;');
        $this->verify('y', 'unknown|null');
    }

    public function testJoin2()
    {
        $this->assuming('x', 'boolean');
        $this->assuming('unknownOrNull', 'unknown|null');
        $this->inMethod('if ($x) { $y = null; } else { $y = $unknownOrNull; }');
        $this->verify('y', 'unknown|null');
    }

    /**
     * @group array
     */
    public function testEmptyArray()
    {
        $this->inMethod('$x = array();');
        $this->verify('x', 'array');
    }

    /**
     * @group array
     */
    public function testArray1()
    {
        $this->assuming('x', $this->createNullableType('array'));
        $this->inMethod('$y = 3; if ($x) { $x = array($y = $x); }');

        // The first type represents an empty array, in that case ``if ($x)`` will evaluate
        // to false, and the IF block will not be reached.
        $this->verify('x', 'array|null|array<integer,array>');
        $this->verify('y', 'integer|array');
    }

    /**
     * @group array
     */
    public function testArray2()
    {
        $this->assuming('x', 'null');
        $this->inMethod('$x = array("foo", "bar");');
        $this->verify('x', 'array<integer,string,{"0":type(string),"1":type(string)}>');
    }

    /**
     * @group array
     */
    public function testArray3()
    {
        $this->assuming('x', 'null');
        $this->inMethod('$x = array("a" => "foo", 4, true);');
        $this->verify('x', 'array<integer|string,string|integer|boolean,{"a":type(string),"0":type(integer),"1":type(boolean)}>');
    }

    /**
     * @group deadlock
     */
    public function testArray4()
    {
        $this->inMethod('foreach ($a as $b) { if (!is_array($x)) { $x = array("value" => $x); } }');

        // X is either already an array, that is is_array($x) is true, or
        // X is is something else in which case it is transformed into an
        // array. So, we either have the generic array type, or the more
        // specific one which only allows strings as key.
        $this->verify('x', 'array|array<string,unknown>');
    }

    /**
     * @group array
     */
    public function testArray5()
    {
        $this->inMethod('$x = array("a", 10 => "b", 3 => "c", "d");');
        $this->verify('x', 'array<integer,string,{"0":type(string),"10":type(string),"3":type(string),"11":type(string)}>');
    }

    /**
     * @group array
     */
    public function testArray6()
    {
        $this->inMethod('$x = array(-5 => "foo", "bar");');
        $this->verify('x', 'array<integer,string,{"-5":type(string),"0":type(string)}>');
    }

    /**
     * @group array
     */
    public function testArray7()
    {
        $this->inMethod('$x = array($foo => "bar", "baz");');
        $this->verify('x', 'array<integer|string,string>');
    }

    /**
     * @group array
     */
    public function testArray8()
    {
        $this->inMethod('$x = array(4, $foo => "bar", "baz");');
        $this->verify('x', 'array<integer|string,string|integer,{"0":type(integer)}>');
    }

    /**
     * @group arrayDim
     */
    public function testArrayDim()
    {
        $this->assuming('x', 'array');
        $this->inMethod('$x[] = "foo"; $x[] = 3;');
        $this->verify('x', 'array<integer,string|integer>');
    }

    /**
     * @group arrayDim
     */
    public function testArrayDim2()
    {
        $this->assuming('x', 'array<integer,string>');
        $this->inMethod('$x["foO"] = true;');
        $this->verify('x', 'array<integer|string,string|boolean,{"foO":type(boolean)}>');
    }

    /**
     * @group arrayDim
     */
    public function testArrayDim3()
    {
        $this->assuming('this->x', 'array');
        $this->inMethod('$this->x[] = "foo";');
        $this->verify('this->x', 'array<integer,string>');
    }

    /**
     * @group arrayDim
     */
    public function testArrayDim4()
    {
        $this->inMethod('$x[] = "foo";');
        $this->verify('x', 'array<integer,string>');
    }

    /**
     * @group arrayDim
     */
    public function testArrayDim5()
    {
        $this->inMethod('self::$x[] = "foO";');
        $this->verify('Foo::$x', 'array<integer,string>');
    }

    /**
     * @group arrayDim
     */
    public function testArrayDim6()
    {
        $this->assuming('x', 'string');
        $this->inMethod('$x[0] = "a";');
        $this->verify('x', 'string');
    }

    /**
     * @group arrayDim
     */
    public function testArrayDim7()
    {
        $this->assuming('this->x', 'string');
        $this->inMethod('$this->x[] = "a";');
        $this->verify('this->x', 'string');
    }

    /**
     * @group arrayItem
     */
    public function testArrayItem()
    {
        $this->assuming('x', 'array<string,all>');
        $this->inMethod('
            $x["foo"] = "foo";

            $a = $x["foo"];
            $b = $x["bar"];
        ');
        $this->verify('a', 'string');
        $this->verify('b', 'all');
    }

    /**
     * @group arrayItem
     */
    public function testArrayItem2()
    {
        $this->assuming('this->x', 'array<string,all>');
        $this->inMethod('
            $this->x["foo"] = "foo";

            $a = $this->x["foo"];
            $b = $this->x["bar"];
        ');
        $this->verify('a', 'string');
        $this->verify('b', 'all');
    }

    /**
     * @group arrayItem
     */
    public function testArrayItem3()
    {
        $this->assuming('x', 'array');
        $this->inMethod('
            $x["foo"] = "bar";
            $x[] = $foo;

            $a = $x["foo"];
            $b = $x["bar"];
        ');
        $this->assuming('a', 'string');
        $this->assuming('b', 'unknown');
    }

        /**
     * @group arrayItem
     */
    public function testArrayItem4()
    {
        $this->assuming('this->x', 'array');
        $this->inMethod('
            $this->x["foo"] = "bar";
            $this->x[] = $foo;

            $a = $this->x["foo"];
            $b = $this->x["bar"];
        ');
        $this->assuming('a', 'string');
        $this->assuming('b', 'unknown');
    }

    /**
     * @group arrayItem
     */
    public function testArrayItem5()
    {
        $this->assuming('x', 'array');
        $this->assuming('a', 'null');
        $this->inMethod('
            if (is_string($x["foo"])) {
                $a = $x["foo"];
            }
            $b = $x["foo"];
        ');
        $this->verify('a', 'null|string');
        $this->verify('b', 'unknown');
    }

    /**
     * @group arrayItem
     */
    public function testArrayItem6()
    {
        $this->assuming('this->x', 'array');
        $this->assuming('a', 'null');
        $this->inMethod('
            if (is_string($this->x["foo"])) {
                $a = $this->x["foo"];
            }
            $b = $this->x["foo"];
        ');
        $this->verify('a', 'null|string');
        $this->verify('b', 'unknown');
    }

    /**
     * @group arrayItem
     */
    public function testArrayItem7()
    {
        $this->assuming('x', 'array');
        $this->inMethod('
           if (is_string($x["foo"])) {
               $x["foo"] = 123;
           } else {
               $x["foo"] = "abc";
           }

           $a = $x["foo"];
        ');
        $this->verify('a', 'string|integer');
    }

    /**
     * @group listAssign
     */
    public function testListAssign()
    {
        $this->inMethod('
            $x = array("foo", 2, array());
            list($a, $b, $c) = $x;
            list(, $d,) = $x;
            list($e,,$f) = $x;
        ');

        $this->verify('a', 'string');
        $this->verify('b', 'integer');
        $this->verify('c', 'array');
        $this->verify('d', 'integer');
        $this->verify('e', 'string');
        $this->verify('f', 'array');
    }

    /**
     * @group listAssign
     */
    public function testListAssign2()
    {
        $this->inMethod('list($a, $b, $c) = array(234, $foo => "bar", true);');
        $this->verify('a', 'integer');
        $this->verify('b', 'unknown_checked');
        $this->verify('c', 'unknown_checked');
    }

    public function testGetElem()
    {
        $this->assuming('x', $this->createNullableType('array'));
        $this->inMethod('$y = 3; if ($x) { $x = $x[$y = $x]; }');
        $this->verify('y', 'integer|array');
        $this->verify('x', 'unknown');
    }

    public function testShortCircuitingAnd()
    {
        $this->assuming('x', 'integer');
        $this->inMethod('$y = null; if ($x && ($y = 3)) { }');
        $this->verify('y', $this->createNullableType('integer'));
    }

    public function testShortCircuitingAnd2()
    {
        $this->assuming('x', 'integer');
        $this->inMethod('$y = null; $z = 4; if ($x && ($y = 3)) { $z = $y; }');
        $this->verify('z', 'integer');
    }

    public function testShortCircuitingOr()
    {
        $this->assuming('x', 'integer');
        $this->inMethod('$y = null; if ($x || ($y = 3)) { }');
        $this->verify('y', 'null|integer');
    }

    public function testShortCircuitingOr2()
    {
        $this->assuming('x', 'integer');
        $this->inMethod('$y = null; $z = 4; if ($x || ($y = 3)) { $z = $y; }');
        $this->verify('z', 'null|integer');
    }

    public function testAssignInCondition()
    {
        $this->assuming('x', 'null|integer');
        $this->inMethod('if (!($y = $x)) { $y = 3; }');
        $this->verify('y', 'integer');
    }

    /**
     * @group conditionAssignments
     */
    public function testTypeOfAndAndOrConditions()
    {
        $this->assuming('x', 'integer');
        $this->assuming('y', 'string');
        $this->inMethod('$z = $x && $y;');
        $this->verify('z', 'boolean');
    }

    /**
     * @group conditionAssignments
     */
    public function testTypeOfAndAndOrConditions2()
    {
        $this->assuming('x', 'integer');
        $this->assuming('y', 'string');
        $this->inMethod('$z = ($x and $y);');
        $this->verify('z', 'boolean');
    }

    /**
     * @group conditionAssignments
     */
    public function testTypeOfAndAndOrConditions3()
    {
        $this->assuming('x', 'integer');
        $this->assuming('y', 'string');
        $this->inMethod('$z = $x || $y;');
        $this->verify('z', 'boolean');
    }

    /**
     * @group conditionAssignments
     */
    public function testTypeOfAndAndOrConditions4()
    {
        $this->assuming('x', 'integer');
        $this->assuming('y', 'string');
        $this->inMethod('$z = ($x or $y);');
        $this->verify('z', 'boolean');
    }

    public function testInstanceOf()
    {
        $class = $this->createClass('Foo');
        $this->assuming('x', 'object');
        $this->inMethod('$y = null; if ($x instanceof Foo) $y = $x;');
        $this->verify('y', $this->createNullableType($class));
    }

    public function testFlattening()
    {
        for ($i=0; $i<LinkedFlowScope::MAX_DEPTH + 1; $i++) {
            $this->assuming('s'.$i, 'all');
        }

        $this->assuming('b', 'boolean');
        $str = 'if ($b) {';
        for ($i=0; $i<LinkedFlowScope::MAX_DEPTH + 1; $i++) {
            $str .= '$s'.$i.' = 1;';
        }
        $str .= '} else {';

        for ($i=0; $i<LinkedFlowScope::MAX_DEPTH + 1; $i++) {
            $str .= '$s'.$i.' = "ONE";';
        }
        $str .= '}';
        $this->inMethod($str);

        $numberOrString = $this->createUnionType('integer', 'string');
        for ($i=0; $i<LinkedFlowScope::MAX_DEPTH + 1; $i++) {
            $this->verify('s'.$i, $numberOrString);
        }
    }

    public function testUnary()
    {
        $this->assuming('x', 'integer');
        $this->inMethod('$y = +$x;');
        $this->verify('y', 'integer');
        $this->inMethod('$z = -$x;');
        $this->verify('z', 'integer');
    }

    public function testAdd1()
    {
        $this->assuming('x', 'integer');
        $this->inMethod('$y = $x + 5;');
        $this->verify('y', 'integer');
    }

    public function testAdd2()
    {
        $this->assuming('x', 'integer');
        $this->inMethod('$y = $x + "5";');
        $this->verify('y', 'integer|double');
    }

    public function testAdd3()
    {
        $this->assuming('x', 'integer');
        $this->inMethod('$y = "5" + $x;');
        $this->verify('y', 'integer|double');
    }

    public function testAssignAdd()
    {
        $this->assuming('x', 'integer');
        $this->inMethod('$x += "5";');
        $this->verify('x', 'integer|double');
    }

    public function testComparison()
    {
        $this->inMethod('$x = "foo"; $y = ($x = 3) < 4;');
        $this->verify('x', 'integer');
        $this->inMethod('$x = "foo"; $y = ($x = 3) > 4;');
        $this->verify('x', 'integer');
        $this->inMethod('$x = "foo"; $y = ($x = 3) <= 4;');
        $this->verify('x', 'integer');
        $this->inMethod('$x = "foo"; $y = ($x = 3) >= 4;');
        $this->verify('x', 'integer');
    }

    public function testThrownExpression()
    {
        $this->inMethod('$x = "foo"; try { throw new Exception($x = 3); } catch (Exception $ex)  {}');
        $this->verify('x', 'integer');
    }

    /**
     * @group null
     */
    public function testIsNull1()
    {
        $this->assuming('x', $this->createNullableType('object'));
        $this->inMethod('if (is_null($x)) { $x = new $foo; }');
        $this->verify('x', 'object');
    }

    /**
     * @group null
     */
    public function testIsNull2()
    {
        $this->assuming('x', $this->createNullableType('object'));
        $this->inMethod('if (true == is_null($x)) { $x = new $foo; }');
        $this->verify('x', 'object');
    }

    /**
     * @group null
     */
    public function testIsNull3()
    {
        $this->assuming('x', $this->createNullableType('object'));
        $this->inMethod('if (!is_null($x)) { } else { $x = new $foo; }');
        $this->verify('x', 'object');
    }

    /**
     * @group null
     */
    public function testIsNull4()
    {
        $this->assuming('x', $this->createNullableType('string'));
        $this->inMethod('if (null === $x || false === $x) { $x = "foo"; }');
        $this->verify('x', 'string');
    }

    /**
     * @group isArray
     */
    public function testIsArray()
    {
        $this->assuming('x', 'integer|array');
        $this->inMethod('$a = is_array($x); if ($a) { $x; $x = 5.4; }');
        $this->verify('x', 'integer|array|double');
    }

    /**
     * @group new
     */
    public function testNew()
    {
        $this->inMethod('$x = new A();');
        $this->verify('x', 'object<A>');
    }

    /**
     * @group new
     */
    public function testNewDynamic()
    {
        $this->assuming('class', 'string');
        $this->inMethod('$x = new $class();');
        $this->verify('x', 'object');
    }

    /**
     * @group assert
     */
    public function testAssert()
    {
        $this->setUpInheritance(array('B' => 'A'));

        $this->assuming('x', 'object<A>');
        $this->inMethod('assert($x instanceof B);');
        $this->verify('x', 'object<B>');
    }

    /**
     * @group cast
     */
    public function testTypeCast()
    {
        $this->assuming('x', 'object');
        $this->inMethod('
            /** @var A $y */
            $y = $x;');

        $this->verify('y', 'object<A>');
    }

    /**
     * @group cast
     */
    public function testTypeCast2()
    {
        $this->assuming('x', 'object');
        $this->inMethod('
            /** @var $y A */
            $y = $x;');

        $this->verify('y', 'object<A>');
    }

    /**
     * @group cast
     */
    public function testTypeCast3()
    {
        $this->assuming('x', 'array<all>');
        $this->inMethod('
            $y = null;
            foreach ($x as $y) {
                /** @var A $y */
                echo "foo";
            }
        ');
        $this->verify('y', 'null|object<A>');
    }

    /**
     * @group cast
     */
    public function testTypeCast4()
    {
        $this->assuming('x', 'array<all>');
        $this->inMethod('
            $y = null;
            /** @var $x array<A> */
            foreach ($x as $y);
        ');
        $this->verify('y', 'null|object<A>');
    }

    /**
     * @group cast
     */
    public function testTypeCast5()
    {
        $this->assuming('x', 'object');
        $this->inMethod('
            /** @var $x A */
            $y = $x;
        ');
        $this->verify('y', 'object<A>');
    }

    /**
     * @group plus
     * @dataProvider getPlusTests
     */
    public function testPlus($aType, $bType, $expectedType)
    {
        $this->assuming('a', $aType);
        $this->assuming('b', $bType);

        $this->inMethod('$x = $a + $b;');
        $this->verify('x', $expectedType);
    }

    public function getPlusTests()
    {
        $tests = array();
        $tests[] = array('array', 'array', 'array');
        $tests[] = array('integer', 'string|null', 'integer|double');
        $tests[] = array('string|null', 'integer', 'integer|double');
        $tests[] = array('all', 'all', 'unknown');
        $tests[] = array('unknown', 'unknown', 'unknown');
        $tests[] = array('unknown', 'integer', 'integer');
        $tests[] = array('double', 'unknown', 'double');
        $tests[] = array('integer', 'double', 'double');
        $tests[] = array('array<integer>', 'array<string>', 'array<string|integer>');

        return $tests;
    }

    protected function setUp()
    {
        $this->registry = new TypeRegistry();
        $this->parser = new \PHPParser_Parser();
    }

    private function createClass($name)
    {
        $class = new Clazz($name);
        $this->registry->registerClass($class);

        return $class;
    }

    private function createNullableType($type)
    {
        return $this->registry->createNullableType($this->resolveType($type));
    }

    private function assuming($symbolName, $type)
    {
        $this->assumptions[$symbolName] = $this->resolveType($type);
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

    private function inMethod($phpCode)
    {
        // Parse the body of the function.
        $ast = $this->parser->parse(new \PHPParser_Lexer('<?php class Foo { function foo() {'.$phpCode.'} }'));

        // Normalize the AST.
        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NameResolver());
        $traverser->addvisitor(new NormalizingNodeVisitor());
        $ast = $traverser->traverse($ast);

        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NodeConnector());
        $traverser->traverse($ast);

        $root = $ast[0];
        $node = $root->stmts[0]->stmts;

        // Create the scope with the assumptions.
        $scopeCreator = new TypedScopeCreator($this->registry);
        $assumedScope = $scopeCreator->createScope($node, $scopeCreator->createScope($root, null));
        foreach ($this->assumptions as $symbolName => $type) {
            $var = $assumedScope->getVar($symbolName);
            if (!$var) {
                $assumedScope->declareVar($symbolName, $type);
            } else {
                $var->setType($type);
            }
        }

        // Create the control graph.
        $cfa = new ControlFlowAnalysis();
        $cfa->process($node);
        $cfg = $cfa->getGraph();

        // Create a simple reverse abstract interpreter.
        $rai = new SemanticReverseAbstractInterpreter($this->registry);
        $fi = new ArrayFunctionInterpreter($this->registry);
        $mi = $this->getMock('Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\MethodInterpreter\MethodInterpreterInterface');

        $commentParser = new \Scrutinizer\PhpAnalyzer\PhpParser\DocCommentParser($this->registry);

        // Do the type inference by data-flow analysis.
        $dfa = new TypeInference($cfg, $rai, $fi, $mi, $commentParser, $assumedScope, $this->registry);
        $dfa->analyze();

        // Get the scope of the implicit return.
        $this->returnScope = $cfg->getImplicitReturn()->getAttribute(DataFlowAnalysis::ATTR_FLOW_STATE_IN);

        $this->astGraph = \Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil::dump($node);
    }

    private function verifySubtypeOf($symbolName, $type)
    {
        $type = $this->resolveType($type);
        $varType = $this->getType($symbolName);

        $this->assertNotNull($varType, 'The variable '.$symbolName.' is missing a type.');
        $this->assertTrue($varType->isSubTypeOf($type), 'The type '.$varType.' of variable '.$symbolName.' is not a subtype of '.$type.'.');
    }

    private function verify($symbolName, $type)
    {
        $type = $this->resolveType($type);
        $cType = $this->getType($symbolName);
        $this->assertTrue($type->equals($cType), sprintf('Expected type "%s" for variable "%s", but got type "%s". Graph: %s', $type, $symbolName, $cType, $this->astGraph));
    }

    private function createUnionType($types)
    {
        if (!is_array($types)) {
            $types = func_get_args();
        }

        return $this->registry->createUnionType($types);
    }

    private function resolveType($type)
    {
        return $this->registry->resolveType($type);
    }

    private function getType($name)
    {
        $this->assertNotNull($this->returnScope, 'The return scope should not be null.');

        $var = $this->returnScope->getSlot($name);
        $this->assertNotNull($var, 'The variable '.$name.' is missing from the scope.');

        return $var->getType();
    }
}