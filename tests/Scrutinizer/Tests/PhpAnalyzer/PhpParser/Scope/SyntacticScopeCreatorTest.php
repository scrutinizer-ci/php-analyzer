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

namespace Scrutinizer\Tests\PhpAnalyzer\PhpParser\Scope;

use JMS\PhpManipulator\PhpParser\NormalizingNodeVisitor;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\SyntacticScopeCreator;

class SyntacticScopeCreatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateScope1()
    {
        $scope = $this->createScope('$a = $b = null;');

        $this->assertEquals(array('param1', 'param2', 'this', 'a', 'b'), $scope->getVarNames());
    }

    private function createScope($src)
    {
        $lexer = new \PHPParser_Lexer('<?php class Foo { public function foo($param1, $param2) { '.$src. ' } }');
        $parser = new \PHPParser_Parser();
        $ast = $parser->parse($lexer);

        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NameResolver());
        $traverser->addVisitor(new NormalizingNodeVisitor());
        $ast = $traverser->traverse($ast);

        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NodeConnector());
        $traverser->traverse($ast);

        $rootNode = $ast[0];
        $scopeRoot = $ast[0]->stmts[0];

        $sc = new SyntacticScopeCreator();

        return $sc->createScope($scopeRoot, new Scope($rootNode));
    }
}