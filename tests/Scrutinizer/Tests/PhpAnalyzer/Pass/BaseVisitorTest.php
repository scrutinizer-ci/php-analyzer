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

abstract class BaseVisitorTest extends \PHPUnit_Framework_TestCase
{
    abstract protected function getVisitors();

    protected function analyzeAst($fixtureName)
    {
        if (!is_file($path = __DIR__.'/Fixture/'.$fixtureName)) {
            throw new \InvalidArgumentException(sprintf('The fixture file "%s" does not exist.', $path));
        }

        $parser = new \PHPParser_Parser();
        $ast = $parser->parse(new \PHPParser_Lexer(file_get_contents($path)));

        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NameResolver());
        foreach ($this->getVisitors() as $visitor) {
            $traverser->addVisitor($visitor);
        }

        return $traverser->traverse($ast);
    }
}