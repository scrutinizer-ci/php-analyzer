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
use JMS\PhpManipulator\PhpParser\BlockNode;
use Scrutinizer\PhpAnalyzer\PhpParser\TreeSerializer;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\MemoryTypeProvider;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\Exception\AnalysisFailedException;
use Scrutinizer\PhpAnalyzer\Model\File;
use Scrutinizer\PhpAnalyzer\Model\FileCollection;

abstract class BaseAnalyzingPassTest extends \PHPUnit_Framework_TestCase
{
    protected $analyzer;
    protected $registry;
    protected $provider;
    protected $file;

    protected function getPasses()
    {
        return array();
    }

    protected function getAnalyzer()
    {
        throw new \LogicException('Either getPasses() must return a non-empty array of passes, or you need to overwrite getAnalyzer().');
    }

    protected function dump($node)
    {
        $s = new TreeSerializer();

        return $s->serialize(is_array($node) ? new BlockNode($node) : $node);
    }

    protected function setUp()
    {
        $passes = $this->getPasses();

        if ($passes) {
            $this->provider = new MemoryTypeProvider();
            $this->registry = new TypeRegistry($this->provider);
            $this->analyzer = new Analyzer($this->registry, PassConfig::createWithPasses($this->getPasses()));

            return;
        }

        $this->analyzer = $this->getAnalyzer();
        $this->registry = $this->analyzer->getTypeRegistry();
        $this->provider = $this->registry->getTypeProvider();
    }

    protected function assertFixedContent($expectedFile)
    {
        if (null === $this->file) {
            throw new \LogicException('assertFixedContent() must be called after analyzeAst().');
        }

        if ( ! is_file($path = __DIR__.'/Fixture/'.$expectedFile)) {
            throw new \InvalidArgumentException(sprintf('The fixture file "%s" does not exist.', $expectedFile));
        }

        $this->assertNotNull($fixedFile = $this->file->getFixedFile());
        $this->assertEquals(file_get_contents($path), $fixedFile->getContent());
    }

    protected function analyzeAst($fixtureName)
    {
        if (!is_file($path = __DIR__.'/Fixture/'.$fixtureName)) {
            throw new \InvalidArgumentException(sprintf('The fixture file "%s" does not exist.', $path));
        }

        $this->file = $file = File::create($fixtureName, file_get_contents($path));
        try {
            $this->analyzer->analyze(new FileCollection(array($this->file)));
        } catch (AnalysisFailedException $ex) {
            throw $ex->getPrevious();
        }

        return array($file->getAst());
    }
}