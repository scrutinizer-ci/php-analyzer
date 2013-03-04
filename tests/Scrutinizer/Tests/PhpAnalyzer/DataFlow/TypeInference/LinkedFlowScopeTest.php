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

use Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface;
use JMS\PhpManipulator\PhpParser\BlockNode;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\FlowScopeInterface;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\LinkedFlowScope;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypeInference;


class LinkedFlowScopeTest extends \PHPUnit_Framework_TestCase
{
    const LONG_CHAIN_LENGTH = 1050;

    private $registry;

    private $blockNode;
    private $methodNode;

    private $globalScope;
    private $localScope;
    private $globalEntry;
    private $localEntry;

    public function testInferSlotType()
    {
        $scope = LinkedFlowScope::createLatticeElement(new Scope($this->getMock('PHPParser_Node')));

        $this->assertNull($scope->getSlot('x'));

        $scope->inferSlotType('x', $this->registry->getNativeType('number'));

        $this->assertNotNull($var = $scope->getSlot('x'));
        $this->assertSame($this->registry->getNativeType('number'), $var->getType());
    }

    public function testGetSlotFromFunctionScope()
    {
        $functionScope = new Scope($this->getMock('PHPParser_Node'));
        $functionScope->declareVar('x', $this->registry->getNativeType('integer'));

        $flowScope = LinkedFlowScope::createLatticeElement($functionScope);
        $this->assertNotNull($var = $flowScope->getSlot('x'));
        $this->assertSame($this->registry->getNativeType('integer'), $var->getType());

        $flowScope = $flowScope->createChildFlowScope();
        $this->assertNotNull($var = $flowScope->getSlot('x'));
        $this->assertSame($this->registry->getNativeType('integer'), $var->getType());
    }

    /**
     * @dataProvider getEqualsTests
     */
    public function testEquals(LatticeElementInterface $a, LatticeElementInterface $b, $expectedOutcome)
    {
        $this->assertSame($expectedOutcome, $a->equals($b));
    }

    public function getEqualsTests()
    {
        $tests = array();

        $scope = new Scope($this->getMock('PHPParser_Node'));
        $tests[] = array(LinkedFlowScope::createLatticeElement($scope), $this->getMock('Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface'), false);

        return $tests;
    }

    public function testOptimize()
    {
        $this->assertSame($this->localEntry, $this->localEntry->optimize());

        $child = $this->localEntry->createChildFlowScope();
        $this->assertSame($this->localEntry, $child->optimize());

        $child->inferSlotType('localB', $this->registry->getNativeType('number'));
        $this->assertSame($child, $child->optimize());
    }

    public function testJoin1()
    {
        $childA = $this->localEntry->createChildFlowScope();
        $childA->inferSlotType('localB', $this->registry->getNativeType('integer'));

        $childAB = $childA->createChildFlowScope();
        $childAB->inferSlotType('localB', $this->registry->getNativeType('string'));

        $childB = $this->localEntry->createChildFlowScope();
        $childB->inferSlotType('localB', $this->registry->getNativeType('boolean'));

        $this->assertSame($this->registry->getNativeType('string'), $childAB->getSlot('localB')->getType());
        $this->assertSame($this->registry->getNativeType('boolean'), $childB->getSlot('localB')->getType());
        $this->assertNull($childB->getSlot('localA')->getType());

        $joined = $this->join($childB, $childAB);
        $this->assertTrue($this->registry->createUnionType(array('string', 'boolean'))->equals($joined->getSlot('localB')->getType()));
        $this->assertNull($joined->getSlot('localA')->getType());

        $joined = $this->join($childAB, $childB);
        $this->assertTrue($this->registry->createUnionType(array('string', 'boolean'))->equals($joined->getSlot('localB')->getType()));
        $this->assertNull($joined->getSlot('localA')->getType());

        $this->assertTrue($this->join($childB, $childAB)->equals($this->join($childAB, $childB)));
    }

    public function testJoin2()
    {
        $childA = $this->localEntry->createChildFlowScope();
        $childA->inferSlotType('localA', $this->getType('string'));

        $childB = $this->localEntry->createChildFlowScope();
        $childB->inferSlotType('globalB', $this->getType('boolean'));

        $this->assertTrue($this->getType('string')->equals($childA->getSlot('localA')->getType()));
        $this->assertTrue($this->getType('boolean')->equals($childB->getSlot('globalB')->getType()));
        $this->assertNull($childB->getSlot('localB')->getType());

        $joined = $this->join($childB, $childA);
        $this->assertTrue($this->getType('string')->equals($joined->getSlot('localA')->getType()));
        $this->assertTrue($this->getType('boolean')->equals($joined->getSlot('globalB')->getType()));

        $joined = $this->join($childA, $childB);
        $this->assertTrue($this->getType('string')->equals($joined->getSlot('localA')->getType()));
        $this->assertTrue($this->getType('boolean')->equals($joined->getSlot('globalB')->getType()));

        $this->assertTrue($this->join($childB, $childA)->equals($this->join($childA, $childB)), 'join() is symmetric.');
    }

    public function testJoin3()
    {
        $this->localScope->declareVar('localC', $this->getType('string'));
        $this->localScope->declareVar('localD', $this->getType('string'));

        $childA = $this->localEntry->createChildFlowScope();
        $childA->inferSlotType('localC', $this->getType('integer'));

        $childB = $this->localEntry->createChildFlowScope();
        $childA->inferSlotType('localD', $this->getType('boolean')); // childA correct here?

        $joined = $this->join($childB, $childA);
        $this->assertTrue($this->registry->createUnionType(array('string', 'integer'))->equals($joined->getSlot('localC')->getType()));
        $this->assertTrue($this->registry->createUnionType(array('string', 'boolean'))->equals($joined->getSlot('localD')->getType()));

        $joined = $this->join($childA, $childB);
        $this->assertTrue($this->registry->createUnionType(array('string', 'integer'))->equals($joined->getSlot('localC')->getType()));
        $this->assertTrue($this->registry->createUnionType(array('string', 'boolean'))->equals($joined->getSlot('localD')->getType()));

        $this->assertTrue($this->join($childB, $childA)->equals($this->join($childA, $childB)));
    }

    public function testLongChain1()
    {
        $chainA = $this->localEntry->createChildFlowScope();
        $chainB = $this->localEntry->createChildFlowScope();

        for ($i=0; $i<self::LONG_CHAIN_LENGTH; $i++) {
            $this->localScope->declareVar('local'.$i);
            $chainA->inferSlotType('local'.$i, $i % 2 === 0 ? $this->getType('integer') : $this->getType('boolean'));
            $chainB->inferSlotType('local'.$i, $i % 3 === 0 ? $this->getType('string') : $this->getType('boolean'));

            $chainA = $chainA->createChildFlowScope();
            $chainB = $chainB->createChildFlowScope();
        }

        $this->verifyLongChains($chainA, $chainB);
    }

    public function testLongChain2()
    {
        $chainA = $this->localEntry->createChildFlowScope();
        $chainB = $this->localEntry->createChildFlowScope();

        for ($i=0; $i<self::LONG_CHAIN_LENGTH * 7; $i++) {
            $this->localScope->declareVar('local'.$i);
            $chainA->inferSlotType('local'.$i, $i % 2 === 0 ? $this->getType('integer') : $this->getType('boolean'));
            $chainB->inferSlotType('local'.$i, $i % 3 === 0 ? $this->getType('string') : $this->getType('boolean'));

            if ($i % 7 === 0) {
                $chainA = $chainA->createChildFlowScope();
                $chainB = $chainB->createChildFlowScope();
            }
        }

        $this->verifyLongChains($chainA, $chainB);
    }

    public function testLongChain3()
    {
        $chainA = $this->localEntry->createChildFlowScope();
        $chainB = $this->localEntry->createChildFlowScope();

        for ($i=0; $i<self::LONG_CHAIN_LENGTH * 7; $i++) {
            if ($i % 7 === 0) {
                $j = (integer) ($i/7);
                $this->localScope->declareVar('local'.$j);
                $chainA->inferSlotType('local'.$j, $j % 2 === 0 ? $this->getType('integer') : $this->getType('boolean'));
                $chainB->inferSlotType('local'.$j, $j % 3 === 0 ? $this->getType('string') : $this->getType('boolean'));
            }

            $chainA = $chainA->createChildFlowScope();
            $chainB = $chainB->createChildFlowScope();
        }

        $this->verifyLongChains($chainA, $chainB);
    }

    protected function setUp()
    {
        $this->registry = new TypeRegistry();

        $this->blockNode = new BlockNode(array());
        $this->methodNode = new \PHPParser_Node_Stmt_ClassMethod('foo');
        $this->methodNode->stmts = new BlockNode(array());

        $this->globalScope = new Scope($this->blockNode);
        $this->globalScope->declareVar('globalA');
        $this->globalScope->declareVar('globalB');

        $this->localScope = new Scope($this->methodNode, $this->globalScope);
        $this->localScope->declareVar('localA');
        $this->localScope->declareVar('localB');

        $this->globalEntry = LinkedFlowScope::createLatticeElement($this->globalScope);
        $this->localEntry = LinkedFlowScope::createLatticeElement($this->localScope);
    }

    private function verifyLongChains(LinkedFlowScope $chainA, LinkedFlowScope $chainB)
    {
        $joined = $this->join($chainA, $chainB);
        for ($i=0; $i<self::LONG_CHAIN_LENGTH; $i++) {
            $type = $i % 2 === 0 ? $this->getType('integer') : $this->getType('boolean');
            $this->assertTrue($type->equals($chainA->getSlot('local'.$i)->getType()));

            $type = $i % 3 === 0 ? $this->getType('string') : $this->getType('boolean');
            $this->assertTrue($type->equals($chainB->getSlot('local'.$i)->getType()));

            $joinedSlotType = $joined->getSlot('local'.$i)->getType();
            if ($i % 6 === 0) {
                $this->assertTrue($this->registry->createUnionType(array('string', 'integer'))->equals($joinedSlotType));
            } else if ($i % 2 === 0) {
                $this->assertTrue($this->registry->createUnionType(array('integer', 'boolean'))->equals($joinedSlotType));
            } else if ($i % 3 === 0) {
                $this->assertTrue($this->registry->createUnionType(array('string', 'boolean'))->equals($joinedSlotType));
            } else {
                $this->assertTrue($this->getType('boolean')->equals($joinedSlotType));
            }
        }

        $this->assertScopesDiffer($chainA, $chainB);
        $this->assertScopesDiffer($chainA, $joined);
        $this->assertScopesDiffer($chainB, $joined);
    }

    public function testFindUniqueSlot()
    {
        $childA = $this->localEntry->createChildFlowScope();
        $childA->inferSlotType('localB', $this->getType('integer'));

        $childAB = $childA->createChildFlowScope();
        $childAB->inferSlotType('localB', $this->getType('string'));

        $childABC = $childAB->createChildFlowScope();
        $childABC->inferSlotType('localA', $this->getType('boolean'));

        $this->assertNull($childABC->findUniqueRefinedSlot($childABC));
        $this->assertTrue($this->getType('boolean')->equals($childABC->findUniqueRefinedSlot($childAB)->getType()));
        $this->assertNull($childABC->findUniqueRefinedSlot($childA));
        $this->assertNull($childABC->findUniqueRefinedSlot($this->localEntry));

        $this->assertTrue($this->getType('string')->equals($childAB->findUniqueRefinedSlot($childA)->getType()));
        $this->assertTrue($this->getType('string')->equals($childAB->findUniqueRefinedSlot($this->localEntry)->getType()));

        $this->assertTrue($this->getType('integer')->equals($childA->findUniqueRefinedSlot($this->localEntry)->getType()));
    }

    public function testDiffer1()
    {
        $childA = $this->localEntry->createChildFlowScope();
        $childA->inferSlotType('localB', $this->getType('integer'));

        $childAB = $childA->createChildFlowScope();
        $childAB->inferSlotType('localB', $this->getType('string'));

        $childABC = $childAB->createChildFlowScope();
        $childABC->inferSlotType('localA', $this->getType('boolean'));

        $childB = $childAB->createChildFlowScope();
        $childB->inferSlotType('localB', $this->getType('string'));

        $childBC = $childB->createChildFlowScope();
        $childBC->inferSlotType('localA', $this->getType('none'));

        $this->assertScopesSame($childAB, $childB);
        $this->assertScopesDiffer($childABC, $childBC);

        $this->assertScopesDiffer($childABC, $childB);
        $this->assertScopesDiffer($childAB, $childBC);

        $this->assertScopesDiffer($childA, $childAB);
        $this->assertScopesDiffer($childA, $childABC);
        $this->assertScopesDiffer($childA, $childB);
        $this->assertScopesDiffer($childA, $childBC);
    }

    public function testDiffer2()
    {
        $childA = $this->localEntry->createChildFlowScope();
        $childA->inferSlotType('localA', $this->getType('integer'));

        $childB = $this->localEntry->createChildFlowScope();
        $childB->inferSlotType('localA', $this->getType('none'));

        $this->assertScopesDiffer($childA, $childB);
    }

    private function assertScopesSame(LinkedFlowScope $a, LinkedFlowScope $b)
    {
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($a));
        $this->assertTrue($a->equals($a));
        $this->assertTrue($b->equals($b));
    }

    private function assertScopesDiffer(LinkedFlowScope $a, LinkedFlowScope $b) {
        $this->assertFalse($a->equals($b));
        $this->assertFalse($b->equals($a));
        $this->assertTrue($a->equals($a));
        $this->assertTrue($b->equals($b));
    }

    private function getType($name)
    {
        return $this->registry->getNativeType($name);
    }

    private function join(FlowScopeInterface $a, FlowScopeInterface $b)
    {
        return call_user_func(TypeInference::createJoinOperation(), array($a, $b));
    }
}