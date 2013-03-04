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

abstract class BaseFixingPassTest extends \PHPUnit_Framework_TestCase
{
    protected $provider;
    protected $registry;
    protected $file;
    protected $analyzer;

    /**
     * @dataProvider getTests
     */
    public function testFixingFiles($testFile)
    {
        $testData = $this->parseTestCase(file_get_contents($testFile));

        $this->file = \Scrutinizer\PhpAnalyzer\Model\File::create('test.php', $testData['before']);
        $this->analyzer->setConfigurationValues($testData['config']);
        $this->analyzer->analyze(new \Scrutinizer\PhpAnalyzer\Model\FileCollection(array($this->file)));

        $this->assertTrue($this->file->hasFixedFile(), 'File has no fixed file.');
        $this->assertEquals($testData['after'], $this->file->getFixedFile()->getContent());
    }

    public function getTests()
    {
        $tests = array();
        foreach (\Symfony\Component\Finder\Finder::create()->name('*.test')->in($this->getTestDir())->files() as $file) {
            $tests[] = array($file->getRealPath());
        }

        return $tests;
    }

    protected function setUp()
    {
        $this->provider = new \Scrutinizer\PhpAnalyzer\PhpParser\Type\MemoryTypeProvider();
        $this->registry = new \Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry($this->provider);
        $this->analyzer = new \Scrutinizer\PhpAnalyzer\Analyzer($this->registry, \Scrutinizer\PhpAnalyzer\PassConfig::createWithPasses($this->getPasses()));
    }

    abstract protected function getTestDir();
    abstract protected function getPasses();

    private function parseTestCase($testContent)
    {
        $tokens = preg_split('#^-- +([a-zA-Z_]+) +--$#m', $testContent, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $data = array();
        for ($i=0,$c=count($tokens); $i<$c; $i += 2) {
            switch ($tokens[$i]) {
                case 'BEFORE':
                    $data['before'] = ltrim($tokens[$i+1]);
                    break;

                case 'AFTER':
                    $data['after'] = ltrim($tokens[$i+1]);
                    break;

                case 'CONFIG':
                    $data['config'] = $tokens[$i+1];
                    break;

                case 'END':
                    if ($i+1 < $c) {
                        $this->fail('"END" must be the last block, but still found blocks after it.');
                    }
                    break;

                default:
                    $this->fail(sprintf('Unknown block "%s". Permissible block names: "BEFORE", "AFTER", and "END".', $tokens[$i]));
            }
        }

        if ( ! isset($data['config'])) {
            $data['config'] = array();
        } else {
            $data['config'] = eval($data['config']);
            $this->assertInternalType('array', $data['config']);
        }

        $this->assertArrayHasKey('before', $data, 'A "BEFORE" block is required in the test file, but did not find one.');
        $this->assertArrayHasKey('after', $data, 'An "AFTER" block is required in the test file, but did not find one.');

        return $data;
    }
}