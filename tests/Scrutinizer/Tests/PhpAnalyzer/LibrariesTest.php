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

use Scrutinizer\PhpAnalyzer\Model\Comment;
use Scrutinizer\PhpAnalyzer\Model\FileCollection;
use Scrutinizer\PhpAnalyzer\Util\TestUtils;
use Symfony\Component\Config\Definition\ArrayNode;

class LibrariesTest extends \PHPUnit_Framework_TestCase
{
    private $em;
    private $analyzer;

    /**
     * @dataProvider getLibraries
     */
    public function testLibrary($zipFile)
    {
        $libName = basename($zipFile, '.zip');

        $config = array();
        if (is_file($configFile = __DIR__.'/libraries/'.$libName.'-config.json')) {
            $config = json_decode(file_get_contents($configFile), true);
        }

        $files = FileCollection::createFromZipFile($zipFile, null, isset($config['filter']) ? $config['filter'] : array());
        $this->analyzer->analyze($files);

        $actualComments = $this->dumpComments($files);
        if ( ! is_file($commentFile = __DIR__.'/libraries/'.$libName.'-comments.txt')) {
            file_put_contents($commentFile.'.tmp', $actualComments);
            $this->fail(sprintf('The comment file "%s" was not found.', $commentFile));
        }
        $this->assertEquals(file_get_contents($commentFile), $actualComments);

        $nonExistentFiles = array();
        foreach ($files as $file) {
            $fixedFilePath = __DIR__.'/libraries/'.$libName.'-'.$file->getName();

            if ( ! $file->hasFixedFileWithChanges()) {
                $this->assertFileNotExists($fixedFilePath.'.diff');

                continue;
            }

            $diff = \Scrutinizer\Util\DiffUtils::generate($file->getContent(), $file->getFixedFile()->getContent());

            if ( ! is_file($fixedFilePath.'.diff')) {
                if ( ! is_dir($dir = dirname($fixedFilePath))) {
                    mkdir($dir, 0777, true);
                }

                file_put_contents($fixedFilePath.'.tmp.diff', $diff);
                $nonExistentFiles[] = $fixedFilePath.'.tmp.diff';

                continue;
            }

            $this->assertEquals(file_get_contents($fixedFilePath.'.diff'), $diff);
        }

        $this->assertCount(0, $nonExistentFiles, "Some files are missing:\n".implode("\n", $nonExistentFiles));
    }

    protected function setUp()
    {
        $this->em = TestUtils::createTestEntityManager();
        $this->analyzer = \Scrutinizer\PhpAnalyzer\Analyzer::create($this->em);

        $defaultConfig = array();
        foreach ($this->analyzer->getPassConfig()->getConfigurablePasses() as $pass) {
            $node = $pass->getConfiguration()->buildTree();
            assert($node instanceof ArrayNode);

            if (array_key_exists('enabled', $node->getChildren())) {
                $defaultConfig[$node->getName()]['enabled'] = true;
            }
        }

        // Some manual changes.
        $defaultConfig['suspicious_code']['overriding_parameter'] = true;
        $defaultConfig['verify_php_doc_comments'] = array_merge($defaultConfig['verify_php_doc_comments'], array(
            'suggest_more_specific_types' => true,
            'ask_for_return_if_not_inferrable' => true,
            'ask_for_param_type_annotation' => true,
        ));

        $this->analyzer->setConfigurationValues($defaultConfig);
    }

    public function getLibraries()
    {
        $libraries = array();
        foreach (\Symfony\Component\Finder\Finder::create()->in(__DIR__.'/libraries')->name('*.zip')->files() as $file) {
            $libraries[] = array($file->getRealPath());
        }

        return $libraries;
    }

    private function dumpComments(FileCollection $col)
    {
        $files = $col->all();
        ksort($files);

        $content = '';
        foreach ($files as $file) {
            if ( ! $file->hasComments()) {
                continue;
            }

            $content .= $file->getName()."\n";
            $content .= "---------------------------------------------------\n";

            foreach ($file->getComments() as $line => $lineComments) {
                foreach ($lineComments as $comment) {
                    /** @var $comment Comment */

                    $content .= "Line $line: ".$comment->getKey()."\n";
                }
            }

            $content .= "\n";
        }

        return $content;
    }
}
