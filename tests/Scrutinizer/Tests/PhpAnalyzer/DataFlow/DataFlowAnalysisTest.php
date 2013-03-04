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

namespace Scrutinizer\Tests\PhpAnalyzer\DataFlow;

use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowGraph;
use Scrutinizer\PhpAnalyzer\ControlFlow\GraphEdge;
use Scrutinizer\PhpAnalyzer\ControlFlow\GraphNode;
use Scrutinizer\PhpAnalyzer\DataFlow\BinaryJoinOp;
use Scrutinizer\PhpAnalyzer\DataFlow\BranchedForwardDataFlowAnalysis;
use Scrutinizer\PhpAnalyzer\DataFlow\DataFlowAnalysis;
use Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface;

class DataFlowAnalysisTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleIf()
    {
        // if (a) { b = 1; } else { b = 1; } c = b;
        $a = new Variable('a');
        $b = new Variable('b');
        $c = new Variable('c');

        $inst1 = new BranchInstruction($a);
        $inst2 = ArithmeticInstruction::newAssignNumberToVariableInstruction($b, 1);
        $inst3 = ArithmeticInstruction::newAssignNumberToVariableInstruction($b, 1);
        $inst4 = ArithmeticInstruction::newAssignVariableToVariableInstruction($c, $b);

        $cfg = new ControlFlowGraph($inst1);
        $cfg->connectIfNotConnected($inst1, GraphEdge::TYPE_ON_FALSE, $inst2);
        $cfg->connectIfNotConnected($inst1, GraphEdge::TYPE_ON_TRUE, $inst3);
        $cfg->connectIfNotConnected($inst2, GraphEdge::TYPE_UNCOND, $inst4);
        $cfg->connectIfNotConnected($inst3, GraphEdge::TYPE_UNCOND, $inst4);
        $n1 = $cfg->getNode($inst1);
        $n2 = $cfg->getNode($inst2);
        $n3 = $cfg->getNode($inst3);
        $n4 = $cfg->getNode($inst4);

        $constProp = new DummyConstPropagation($cfg);
        $constProp->analyze();

        // We cannot conclude anything from if (a).
        $this->verifyInHas($n1, $a, null);
        $this->verifyInHas($n1, $b, null);
        $this->verifyInHas($n1, $c, null);
        $this->verifyOutHas($n1, $a, null);
        $this->verifyOutHas($n1, $b, null);
        $this->verifyOutHas($n1, $c, null);

        // We can conclude b = 1 after the instruction.
        $this->verifyInHas($n2, $a, null);
        $this->verifyInHas($n2, $b, null);
        $this->verifyInHas($n2, $c, null);
        $this->verifyOutHas($n2, $a, null);
        $this->verifyOutHas($n2, $b, 1);
        $this->verifyOutHas($n2, $c, null);

        // Same as above.
        $this->verifyInHas($n3, $a, null);
        $this->verifyInHas($n3, $b, null);
        $this->verifyInHas($n3, $c, null);
        $this->verifyOutHas($n3, $a, null);
        $this->verifyOutHas($n3, $b, 1);
        $this->verifyOutHas($n3, $c, null);

        // After the merge, we should still have b = 1.
        $this->verifyInHas($n4, $a, null);
        $this->verifyInHas($n4, $b, 1);
        $this->verifyInHas($n4, $c, null);
        $this->verifyOutHas($n4, $a, null);
        // After the instruction, both b and c are 1.
        $this->verifyOutHas($n4, $b, 1);
        $this->verifyOutHas($n4, $c, 1);
    }

    public function testSimpleLoop()
    {
        // a = 0; do { a = a + 1 } while (b); c = a;
        $a = new Variable('a');
        $b = new Variable('b');
        $c = new Variable('c');

        $inst1 = ArithmeticInstruction::newAssignNumberToVariableInstruction($a, 0);
        $inst2 = new ArithmeticInstruction($a, $a, '+', new Number(1));
        $inst3 = new BranchInstruction($b);
        $inst4 = ArithmeticInstruction::newAssignVariableToVariableInstruction($c, $a);

        $cfg = new ControlFlowGraph($inst1);
        $cfg->connectIfNotConnected($inst1, GraphEdge::TYPE_UNCOND, $inst2);
        $cfg->connectIfNotConnected($inst2, GraphEdge::TYPE_UNCOND, $inst3);
        $cfg->connectifNotConnected($inst3, GraphEdge::TYPE_ON_TRUE, $inst2);
        $cfg->connectIfNotConnected($inst3, GraphEdge::TYPE_ON_FALSE, $inst4);
        $n1 = $cfg->getNode($inst1);
        $n2 = $cfg->getNode($inst2);
        $n3 = $cfg->getNode($inst3);
        $n4 = $cfg->getNode($inst4);

        $constProp = new DummyConstPropagation($cfg);
        // This will also show that the framework terminates properly.
        $constProp->analyze();

        // a = 0 is the only thing we know.
        $this->verifyInHas($n1, $a, null);
        $this->verifyInHas($n1, $b, null);
        $this->verifyInHas($n1, $c, null);
        $this->verifyOutHas($n1, $a, 0);
        $this->verifyOutHas($n1, $b, null);
        $this->verifyOutHas($n1, $c, null);

        // Nothing is provable in this program, so confirm that we haven't
        // erroneously "proven" something.
        $this->verifyInHas($n2, $a, null);
        $this->verifyInHas($n2, $b, null);
        $this->verifyInHas($n2, $c, null);
        $this->verifyOutHas($n2, $a, null);
        $this->verifyOutHas($n2, $b, null);
        $this->verifyOutHas($n2, $c, null);

        $this->verifyInHas($n3, $a, null);
        $this->verifyInHas($n3, $b, null);
        $this->verifyInHas($n3, $c, null);
        $this->verifyOutHas($n3, $a, null);
        $this->verifyOutHas($n3, $b, null);
        $this->verifyOutHas($n3, $c, null);

        $this->verifyInHas($n4, $a, null);
        $this->verifyInHas($n4, $b, null);
        $this->verifyInHas($n4, $c, null);
        $this->verifyOutHas($n4, $a, null);
        $this->verifyOutHas($n4, $b, null);
        $this->verifyOutHas($n4, $c, null);
    }

    public function testBranchedSimpleIf()
    {
        // if (a) { a = 0; } else { b = 0; } c = b;
        $a = new Variable('a');
        $b = new Variable('b');
        $c = new Variable('c');

        $inst1 = new BranchInstruction($a);
        $inst2 = ArithmeticInstruction::newAssignNumberToVariableInstruction($a, 0);
        $inst3 = ArithmeticInstruction::newAssignNumberToVariableInstruction($b, 0);
        $inst4 = ArithmeticInstruction::newAssignVariableToVariableInstruction($c, $b);

        $cfg = new ControlFlowGraph($inst1);
        $cfg->connectIfNotConnected($inst1, GraphEdge::TYPE_ON_TRUE, $inst2);
        $cfg->connectIfNotConnected($inst1, GraphEdge::TYPE_ON_FALSE, $inst3);
        $cfg->connectIfNotConnected($inst2, GraphEdge::TYPE_UNCOND, $inst4);
        $cfg->connectIfNotConnected($inst3, GraphEdge::TYPE_UNCOND, $inst4);
        $n1 = $cfg->getNode($inst1);
        $n2 = $cfg->getNode($inst2);
        $n3 = $cfg->getNode($inst3);
        $n4 = $cfg->getNode($inst4);

        $constProp = new BranchedDummyConstPropagation($cfg);
        $constProp->analyze();

        // We cannot conclude anything from if(a).
        $this->verifyInHas($n1, $a, null);
        $this->verifyInHas($n1, $b, null);
        $this->verifyInHas($n1, $c, null);

        // Nothing is known on the true branch.
        $this->verifyInHas($n2, $a, null);
        $this->verifyInHas($n2, $b, null);
        $this->verifyInHas($n2, $c, null);

        // Verify that we have a = 0 on the false branch.
        $this->verifyInHas($n3, $a, 0);
        $this->verifyInHas($n3, $b, null);
        $this->verifyInHas($n3, $c, null);

        // After the merge we should still have a = 0.
        $this->verifyInHas($n4, $a, 0);
    }

    private function verifyInHas(GraphNode $node, Variable $var, $constant)
    {
        $fState = $node->getAttribute(DataFlowAnalysis::ATTR_FLOW_STATE_IN);

        if (null === $constant) {
            $this->assertFalse(isset($fState->constMap[$var]));
        } else {
            $this->assertTrue(isset($fState->constMap[$var]));
            $this->assertSame($constant, $fState->constMap[$var]);
        }
    }

    private function verifyOutHas(GraphNode $node, Variable $var, $constant)
    {
        $fState = $node->getAttribute(DataFlowAnalysis::ATTR_FLOW_STATE_OUT);

        if (null === $constant) {
            $this->assertFalse(isset($fState->constMap[$var]));
        } else {
            $this->assertTrue(isset($fState->constMap[$var]));
            $this->assertSame($constant, $fState->constMap[$var]);
        }
    }
}

abstract class Value
{
    public function isNumber()
    {
        return $this instanceof Number;
    }

    public function isVariable()
    {
        return $this instanceof Variable;
    }

    public function equals($that)
    {
        return $this === $that;
    }
}

class Variable extends Value
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function equals($that)
    {
        if (!$that instanceof Variable) {
            return false;
        }

        return $that->name === $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }
}

class Number extends Value
{
    private $value;

    public function __construct($v)
    {
        $this->value = (integer) $v;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}

abstract class Instruction
{
    private $order;

    public function isArithmetic()
    {
        return $this instanceof ArithmeticInstruction;
    }

    public function isBranch()
    {
        return $this instanceof BranchInstruction;
    }
}

class ArithmeticInstruction extends Instruction
{
    private $operation;
    private $operand1;
    private $operand2;
    private $result;

    public function __construct(Variable $res, Value $op1, $o, Value $op2)
    {
        $this->result = $res;
        $this->operand1 = $op1;
        $this->operand2 = $op2;
        $this->operation = $o;
    }

    public function getOperator()
    {
        return $this->operation;
    }

    public function setOperator($op)
    {
        $this->operation = $op;
    }

    public function getOperand1()
    {
        return $this->operand1;
    }

    public function setOperand1(Value $operand1)
    {
        $this->operand1 = $operand1;
    }

    public function getOperand2()
    {
        return $this->operand2;
    }

    public function setOperand2(Value $operand2)
    {
        $this->operand2 = $operand2;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult(Variable $result)
    {
        $this->result = $result;
    }

    public function __toString()
    {
        $str = '';
        $str .= $this->result;
        $str .= ' = ';
        $str .= $this->operand1;
        $str .= $this->operation;
        $str .= $this->operand2;

        return $str;
    }

    public static function newAssignNumberToVariableInstruction(Variable $res, $num)
    {
        return new ArithmeticInstruction($res, new Number($num), '+', new Number(0));
    }

    public static function newAssignVariableToVariableInstruction(Variable $lhs, Variable $rhs)
    {
        return new ArithmeticInstruction($lhs, $rhs, '+', new Number(0));
    }
}

class BranchInstruction extends Instruction {
    private $condition;

    public function __construct(Value $cond)
    {
        $this->condition = $cond;
    }

    public function getCondition()
    {
        return $this->condition;
    }

    public function setCondition(Value $condition)
    {
        $this->condition = $condition;
    }
}

/**
 * A lattice to represent constant states. Each variable of the program will
 * have a lattice defined as:
 *
 * <pre>
 *        TOP
 *   / / |         \
 *  0  1 2 3 ..... MAX_VALUE
 *  \  \ |         /
 *       BOTTOM
 * </pre>
 *
 * Where BOTTOM represents the variable is not a constant.
 * <p>
 * This class will represent a product lattice of each variable's lattice. The
 * whole lattice is store in a {@code HashMap}. If variable {@code x} is
 * defined to be constant 10. The map will contain the value 10 with the
 * variable {@code x} as key. Otherwise, {@code x} is not a constant.
 */
class ConstPropLatticeElement implements LatticeElementInterface
{
    public $constMap;
    public $isTop;

    public function __construct($isTop = false)
    {
        $this->isTop = (boolean) $isTop;
        $this->constMap = new \SplObjectStorage();
    }

    public function __clone()
    {
        $constMap = new \SplObjectStorage();
        foreach ($this->constMap as $var) {
            $constMap[$var] = $this->constMap[$var];
        }
        $this->constMap = $constMap;
    }

    public function __toString()
    {
        if ($this->isTop) {
            return 'TOP';
        }

        $str = '{';
        foreach ($this->constMap as $var) {
            $str .= $var;
            $str .= '=';
            $str .= $this->constMap[$var];
            $str .= ' ';
        }
        $str .= '}';

        return $str;
    }

    public function equals(LatticeElementInterface $that)
    {
        if (!$that instanceof ConstPropLatticeElement) {
            return false;
        }

        if ($that->isTop !== $this->isTop) {
            return false;
        }

        foreach ($this->constMap as $var) {
            if (!isset($that->constMap[$var])) {
                return false;
            }

            if ($this->constMap[$var] !== $that->constMap[$var]) {
                return false;
            }
        }

        foreach ($that->constMap as $var) {
            if (!isset($this->constMap[$var])) {
                return false;
            }

            if ($this->constMap[$var] !== $that->constMap[$var]) {
                return false;
            }
        }

        return true;
    }
}

class ConstPropJoinOp
{
    private $binOp;

    public function __construct()
    {
        $this->binOp = new BinaryJoinOp(array($this, 'applyBinary'));
    }

    public function __invoke(array $values)
    {
        return call_user_func($this->binOp, $values);
    }

    public function applyBinary(LatticeElementInterface $a, LatticeElementInterface $b)
    {
        $result = new ConstPropLatticeElement();

        if ($a->isTop) {
            return clone $a;
        }

        if ($b->isTop) {
            return clone $b;
        }

        foreach ($a->constMap as $var) {
            if (isset($b->constMap[$var])) {
                $number = $b->constMap[$var];

                if ($a->constMap[$var] === $number) {
                    $result->constMap[$var] = $number;
                }
            }
        }

        return $result;
    }
}

class DummyConstPropagation extends DataFlowAnalysis
{
    public function __construct(ControlFlowGraph $cfg)
    {
        parent::__construct($cfg, new ConstPropJoinOp());
    }

    protected function isForward()
    {
        return true;
    }

    protected function flowThrough($node, LatticeElementInterface $input)
    {
        if (!$node instanceof Instruction) {
            throw new \InvalidArgumentException('$node must be an instance of Instruction.');
        }

        if ($node->isBranch()) {
            return clone $input;
        }

        return self::flowThroughArithmeticInstruction($node, $input);
    }

    protected function createEntryLattice()
    {
        return new ConstPropLatticeElement();
    }

    protected function createInitialEstimateLattice()
    {
        return new ConstPropLatticeElement(true);
    }

    public static function flowThroughArithmeticInstruction(ArithmeticInstruction $aInst, ConstPropLatticeElement $input)
    {
        $out = clone $input;

        // Try to see if left is a number. If it is a variable, it might already
        // be a constant coming in.
        $leftConst = null;
        if ($aInst->getOperand1()->isNumber()) {
            $leftConst = $aInst->getOperand1()->getValue();
        } else {
            if (isset($input->constMap[$aInst->getOperand1()])) {
                $leftConst = $input->constMap[$aInst->getOperand1()];
            }
        }

        // Do the same thing to the right.
        $rightConst = null;
        if ($aInst->getOperand2()->isNumber()) {
            $rightConst = $aInst->getOperand2()->getValue();
        } else {
            if (isset($input->constMap[$aInst->getOperand2()])) {
                $rightConst = $input->constMap[$aInst->getOperand2()];
            }
        }

        // If both are known constants, we can perform the operation.
        if (null !== $leftConst && null !== $rightConst) {
            $constResult = null;
            switch ($aInst->getOperator()) {
                case '+':
                    $constResult = $leftConst + $rightConst;
                    break;

                case '-':
                    $constResult = $leftConst - $rightConst;
                    break;

                case '*':
                    $constResult = $leftConst * $rightConst;
                    break;

                case '/':
                    $constResult = $leftConst / $rightConst;
                    break;
            }

            $out->constMap[$aInst->getResult()] = $constResult;
        } else {
            unset($out->constMap[$aInst->getResult()]);
        }

        return $out;
    }
}

class BranchedDummyConstPropagation extends BranchedForwardDataFlowAnalysis
{
    public function __construct(ControlFlowGraph $cfg)
    {
        parent::__construct($cfg, new ConstPropJoinOp());
    }

    protected function branchedFlowThrough($node, LatticeElementInterface $input)
    {
        $result = array();
        $outEdges = $this->cfg->getOutEdges($node);

        if ($node->isArithmetic()) {
            assert(count($outEdges) < 2);

            $aResult = DummyConstPropagation::flowThroughArithmeticInstruction($node, $input);
            foreach ($outEdges as $stuff) {
                $result[] = $aResult;
            }
        } else {
            foreach ($outEdges as $branch) {
                $edgeResult = clone $input;
                if ($branch->getType() === GraphEdge::TYPE_ON_FALSE
                        && $node->getCondition()->isVariable()) {
                    $edgeResult->constMap[$node->getCondition()] = 0;
                }

                $result[] = $edgeResult;
            }
        }

        return $result;
    }

    protected function createEntryLattice()
    {
        return new ConstPropLatticeElement();
    }

    protected function createInitialEstimateLattice()
    {
        return new ConstPropLatticeElement(true);
    }
}