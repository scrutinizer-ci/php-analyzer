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

namespace Scrutinizer\Tests\PhpAnalyzer\ControlFlow;

use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowAnalysis;
use Scrutinizer\PhpAnalyzer\ControlFlow\GraphvizSerializer;
use JMS\PhpManipulator\PhpParser\BlockNode;
use JMS\PhpManipulator\PhpParser\NormalizingNodeVisitor;
use Symfony\Component\Finder\Finder;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTests
     */
    public function testIntegration($sourceFile)
    {
        $parser = new \PHPParser_Parser();
        $ast = $parser->parse(new \PHPParser_Lexer(file_get_contents($sourceFile)));

        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NameResolver());
        $traverser->addVisitor(new NormalizingNodeVisitor());
        $ast = $traverser->traverse($ast);

        if (count($ast) > 1) {
            $ast = array(new BlockNode($ast));
        }

        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NodeConnector());
        $traverser->traverse($ast); // do _NOT_ assign the ast here as it clones the nodes on traversal and leads to problems

        $cfa = new ControlFlowAnalysis();
        $cfa->process($ast[0]);
        $cfg = $cfa->getGraph();

        $serializer = new GraphvizSerializer();
        $dot = $serializer->serialize($cfg);

        $dotFile = substr($sourceFile, 0, -4).'.dot';

        if (!is_file($dotFile)) {
            file_put_contents($dotFile.'.tmp', $dot);
            $this->fail(sprintf('Dotfile "%s" does not exist, wrote current output to "%s".', $dotFile, basename($dotFile.'.tmp')));
        }

        $this->assertSame($dot, file_get_contents($dotFile));
    }

    public function getTests()
    {
        $tests = array();

        try {
            foreach (Finder::create()->in(__DIR__.'/Fixture/Integration/')->name('*.php') as $file) {
                $tests[] = array($file->getRealpath());
            }
        } catch (\Exception $ex) {
            echo 'Error when loading integration tests: '.$ex->getMessage();
            echo '!!! No Integration Tests were performed !!!';
        }

        return $tests;
    }
}