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
use Scrutinizer\PhpAnalyzer\Model\File;
use Scrutinizer\PhpAnalyzer\Model\FileCollection;
use Scrutinizer\Util\DiffUtils;

abstract class BaseReviewingPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTests
     */
    public function testAnalyze($filename)
    {
        $test = $this->parseTestCase($filename);
        $testName = basename($filename);

        $phpFile = File::create($test['filename'], $test['code']);
        if (!empty($test['diff'])) {
            $phpFile->setDiff($test['diff']);
        }

        $expectedComments = $test['comments'];
        $config = $test['config'];

        $analyzer = $this->getAnalyzer();
        $analyzer->setConfigurationValues($config);
        $analyzer->analyze(new FileCollection(array($phpFile)));

        $actualComments = $phpFile->getComments();
        foreach ($expectedComments as $line => $comments) {
            $this->assertTrue(isset($actualComments[$line]), sprintf('Expected comments for line "%d", but got none in test "%s". Comments: %s', $line, $testName, var_export($actualComments, true)));
            $this->assertSame(count($comments), count($actualComments[$line]), sprintf('Expected "%d" comments for line "%d", but got "%d" comments. Comments: %s', count($comments), $line, count($actualComments[$line]), var_export($actualComments, true)));

            foreach ($comments as $comment) {
                $found = false;
                foreach ($actualComments[$line] as $actualComment) {
                    if (false !== strpos((string) $actualComment, $comment)) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $availableComments = "\n";
                    foreach ($actualComments[$line] as $aComment) {
                        $availableComments .= $aComment."\n";
                    }

                    $this->fail(sprintf('Expected comment "%s" on line "%d", but did not find it. Available Comments: %s', $comment, $line, $availableComments));
                }
            }
        }

        foreach ($actualComments as $line => $actualComment) {
            $this->assertTrue(isset($expectedComments[$line]), sprintf('Found comment "%s" on line "%d", but did not expect it.', (string) $actualComment[0], $line));
        }

        if (null !== $test['after']) {
            $this->assertTrue($phpFile->hasFixedFile());
            $this->assertEquals($test['after'], $phpFile->getFixedFile()->getContent());
        }
    }

    protected function getAnalyzer()
    {
        return new Analyzer(null, $this->getPassConfig());
    }

    public function getTests()
    {
        $tests = array();
        foreach ($this->getTestFinder() as $file) {
            $tests[] = array($file->getRealPath());
        }

        if ( ! $tests) {
            die (get_class($this).' did not find any tests.'.PHP_EOL);
            exit(1);
        }

        return $tests;
    }

    protected function getPassConfig()
    {
        return PassConfig::createWithPasses($this->getPasses());
    }

    protected function getPasses()
    {
        return array();
    }

    protected function getTestFinder()
    {
        if (null === $dir = $this->getTestDir()) {
            echo "getTestDir(), or getTestFinder() must be overwritten for ".get_class($this).".".PHP_EOL;
            exit(1);
        }

        return \Symfony\Component\Finder\Finder::create()->in($dir)->files();
    }

    protected function getTestDir()
    {
        return null;
    }

    private function parseTestCase($filename)
    {
        $content = file_get_contents($filename);

        $test = array('filename' => 'foo.php', 'code' => '', 'diff' => '', 'after' => null, 'comments' => array(), 'config' => '', 'new_file' => null, 'original_file' => null);

        $block = null;
        foreach (explode("\n", $content) as $line) {
            if (0 === strpos($line, '-- DIFF --')) {
                $block = 'diff';
                continue;
            }

            if (0 === strpos($line, '-- AFTER --')) {
                $block = 'after';
                continue;
            }

            if (0 === strpos($line, '-- COMMENTS --')) {
                $block = 'comments';
                continue;
            }

            if (0 === strpos($line, '-- CONFIG --')) {
                $block = 'config';
                continue;
            }

            if (0 === strpos($line, '-- FILENAME --')) {
                $block = 'filename';
                continue;
            }

            if (0 === strpos($line, '-- ORIGINAL_FILE --')) {
                $block = 'original_file';
                continue;
            }

            if (0 === strpos($line, '-- NEW_FILE --')) {
                $block = 'new_file';
                continue;
            }

            switch ($block) {
                case 'config':
                    if ('' !== trim($line)) {
                        $test['config'] .= $line;
                    }
                    break;

                case 'new_file':
                case 'original_file':
                case 'filename':
                    if ('' !== trim($line)) {
                        $test[$block] = trim($line);
                    }
                    break;

                case 'comments':
                    if ('' === trim($line)) {
                        break;
                    }

                    if (!preg_match('/^Line ([0-9]+): (.*)/', $line, $match)) {
                        throw new \RuntimeException('Invalid Comment Line: '.$line);
                    }

                    $test['comments'][(int) $match[1]][] = $match[2];
                    break;

                case 'diff':
                    if ('' !== trim($line)) {
                        $test['diff'] .= $line."\n";
                    }
                    break;

                case 'after':
                    $test['after'] .= $line."\n";
                    break;

                default:
                    $test['code'] .= $line."\n";
            }
        }

        if (null !== $test['new_file'] && null !== $test['original_file']) {
            if ( ! is_file($newFile = dirname($filename).'/'.$test['new_file'])) {
                throw new \InvalidArgumentException(sprintf('New file not found at "%s".', $newFile));
            }

            if ( ! is_file($originalFile = dirname($filename).'/'.$test['original_file'])) {
                throw new \InvalidArgumentException(sprintf('Original file not found at "%s".', $originalFile));
            }

            $test['code'] = file_get_contents($newFile);
            $test['diff'] = DiffUtils::generate(file_get_contents($originalFile), $test['code']);
        }

        if ('' === $test['config']) {
            $test['config'] = array();
        } else {
            $test['config'] = eval($test['config']);
            if (!is_array($test['config'])) {
                throw new \RuntimeException('Config must be an array, but got '.var_export($test['config'], true));
            }
        }

        return $test;
    }
}