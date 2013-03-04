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

use JMS\PhpManipulator\PhpParser\BlockNode;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;

class NodeTraversalTest extends \PHPUnit_Framework_TestCase
{
    public function testScopeChanges()
    {
        $function = new \PHPParser_Node_Stmt_Function('foo', array(
            'stmts' => $functionBlock = new BlockNode(array(
                new \PHPParser_Node_Expr_Assign(
                    new \PHPParser_Node_Expr_Variable('foo'),
                    $closure = new \PHPParser_Node_Expr_Closure(array(
                        'stmts' => $closureBlock = new BlockNode(array(
                        ))
                    ))
                )
            )),
        ));

        $callback = $this->getMock('Scrutinizer\PhpAnalyzer\PhpParser\Traversal\CallbackInterface');
        $functionScope = $closureScope = null;
        $t = new NodeTraversal($callback);

        $callback->expects($this->atLeastOnce())
            ->method('shouldTraverse')
            ->will($this->returnValue(true));

        $self = $this;
        $callback->expects($this->atLeastOnce())
            ->method('visit')
            ->will($this->returnCallback(function(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
                    use (&$functionScope, &$closureScope, $function, $closure, $self) {
                if (null === $parent) {
                    $functionScope = $t->getScope();

                    $self->assertSame(1, $t->getScopeDepth());
                    $self->assertSame($function, $t->getScopeRoot());
                }

                if  ($parent instanceof \PHPParser_Node_Expr_Closure) {
                    $closureScope = $t->getScope();

                    $self->assertSame(2, $t->getScopeDepth());
                    $self->assertSame($closure, $t->getScopeRoot());
                }
            }));

        $t->traverse($function);

        $this->assertInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope', $functionScope);
        $this->assertInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope', $closureScope);
        $this->assertNotSame($functionScope, $closureScope);
    }

    public function testTraverseCatch()
    {
        $catch = new \PHPParser_Node_Stmt_Catch($name = new \PHPParser_Node_Name(array('foo')), 'foo');
        $catchBlock = $catch->stmts = new BlockNode(array());

        $callback = $this->getMock('Scrutinizer\PhpAnalyzer\PhpParser\Traversal\CallbackInterface');
        $t = new NodeTraversal($callback);

        $callback->expects($this->at(0))
            ->method('shouldTraverse')
            ->with($this->anything(), $catch, null)
            ->will($this->returnValue(true));
        $callback->expects($this->at(1))
            ->method('shouldTraverse')
            ->with($this->anything(), $name, $catch)
            ->will($this->returnValue(false));
        $callback->expects($this->at(2))
            ->method('shouldTraverse')
            ->with($this->anything(), $catchBlock, $catch)
            ->will($this->returnValue(true));
        $callback->expects($this->at(3))
            ->method('visit')
            ->with($this->anything(), $catchBlock, $catch);
        $callback->expects($this->at(4))
            ->method('visit')
            ->with($this->anything(), $catch, null);

        $t->traverse($catch);
    }

    public function testTraverse()
    {
        $function = new \PHPParser_Node_Stmt_Function('foo', array(
            'stmts' => $functionBlock = new BlockNode(array(
                 $try = new \PHPParser_Node_Stmt_TryCatch(array(), array(
                     $catch1 = new \PHPParser_Node_Stmt_Catch(new \PHPParser_Node_Name(array('Foo')), 'ex'),
                     $catch2 = new \PHPParser_Node_Stmt_Catch(new \PHPParser_Node_Name(array('Bar')), 'ex'),
                 )),
                 $echo = new \PHPParser_Node_Stmt_Echo(array(new \PHPParser_Node_Scalar_String('foo'))),
            ))
        ));
        $tryBlock = $try->stmts = new BlockNode(array(
            $throw = new \PHPParser_Node_Stmt_Throw(new \PHPParser_Node_Expr_Variable('foo'))
        ));
        $catch1Block = $catch1->stmts = new BlockNode(array());
        $catch2Block = $catch2->stmts = new BlockNode(array());

        $callback = $this->getMock('Scrutinizer\PhpAnalyzer\PhpParser\Traversal\CallbackInterface');
        $t = new NodeTraversal($callback);

        $callback->expects($this->at(0))
            ->method('shouldTraverse')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $function, null)
            ->will($this->returnValue(true));
        $callback->expects($this->at(1))
            ->method('shouldTraverse')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $functionBlock, $function)
            ->will($this->returnValue(true));
        $callback->expects($this->at(2))
            ->method('shouldTraverse')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $try, $functionBlock)
            ->will($this->returnValue(true));
        $callback->expects($this->at(3))
            ->method('shouldTraverse')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $tryBlock, $try)
            ->will($this->returnValue(true));
        $callback->expects($this->at(4))
            ->method('shouldTraverse')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $throw, $tryBlock)
            ->will($this->returnValue(false));
        $callback->expects($this->at(5))
            ->method('visit')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $tryBlock, $try);
        $callback->expects($this->at(6))
            ->method('shouldTraverse')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $catch1, $try)
            ->will($this->returnValue(true));
        $callback->expects($this->at(7))
            ->method('shouldTraverse')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $this->isInstanceOf('PHPParser_Node_Name'), $catch1)
            ->will($this->returnValue(false));
        $callback->expects($this->at(8))
            ->method('shouldTraverse')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $catch1Block, $catch1)
            ->will($this->returnValue(true));
        $callback->expects($this->at(9))
            ->method('visit')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $catch1Block, $catch1);
        $callback->expects($this->at(10))
            ->method('visit')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $catch1, $try);
        $callback->expects($this->at(11))
            ->method('shouldTraverse')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $catch2, $try)
            ->will($this->returnValue(false));
        $callback->expects($this->at(12))
            ->method('visit')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $try, $functionBlock);
        $callback->expects($this->at(13))
            ->method('shouldTraverse')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $echo, $functionBlock)
            ->will($this->returnValue(false));
        $callback->expects($this->at(14))
            ->method('visit')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $functionBlock, $function);
        $callback->expects($this->at(15))
            ->method('visit')
            ->with($this->isInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal'), $function, null);

        $t->traverse($function);
    }
}