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

use Scrutinizer\PhpAnalyzer\Model\File;

abstract class BasePassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getCodingStyleTests
     */
    public function testAnalyze($testName, $phpFile, $expectedComments)
    {
        $pass = $this->getPass();
        $pass->analyze($phpFile);

        $actualComments = $phpFile->getComments();
        foreach ($expectedComments as $line => $comments) {
            $this->assertTrue(isset($actualComments[$line]), sprintf('Expected comments for line "%d", but got none in test "%s". Comments: %s', $line, $testName, var_export($actualComments, true)));
            $this->assertSame(count($comments), count($actualComments[$line]), sprintf('Expected "%d" comments for line "%d", but got "%d" comments.', count($comments), $line, count($actualComments[$line])));

            foreach ($comments as $comment) {
                $found = false;
                foreach ($actualComments[$line] as $actualComment) {
                    if (false !== strpos((string) $actualComment, $comment)) {
                        $found = true;
                        break;
                    }
                }

                $this->assertTrue($found, sprintf('Expected comment "%s" on line "%d", but did not find it. Available Comments: %s', $comment, $line, var_export($actualComment, true)));
            }
        }

        foreach ($actualComments as $line => $actualComment) {
            $this->assertTrue(isset($expectedComments[$line]), sprintf('Found comment "%s" on line "%d", but did not expect it.', (string) $actualComment[0], $line));
        }
    }

    public function getCodingStyleTests()
    {
        $testDir = realpath($this->getTestDir());

        try {
            $tests = array();
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($testDir)) as $file) {
                $test = $this->parseTestCase(file_get_contents($file->getRealpath()));

                $phpFile = File::create($file->getFilename().'.php', $test['code']);
                if (!empty($test['diff'])) {
                    $phpFile->setDiff($test['diff']);
                }

                $tests[] = array($file->getFilename(), $phpFile, $test['comments']);
            }
        } catch (\Exception $ex) {
            echo "Could not load test cases\n";
            echo $ex->getMessage();
            exit;
        }

        return $tests;
    }

    abstract protected function getPass();
    abstract protected function getTestDir();

    private function parseTestCase($content)
    {
        $test = array('code' => '', 'diff' => '', 'comments' => array());

        $block = null;
        foreach (explode("\n", $content) as $line) {
            if (0 === strpos($line, '-- DIFF --')) {
                $block = 'diff';
                continue;
            }

            if (0 === strpos($line, '-- COMMENTS --')) {
                $block = 'comments';
                continue;
            }

            switch ($block) {
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

                default:
                    $test['code'] .= $line."\n";
            }
        }

        return $test;
    }
}