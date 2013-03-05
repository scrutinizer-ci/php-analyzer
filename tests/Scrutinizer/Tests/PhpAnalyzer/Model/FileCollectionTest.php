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

namespace Scrutinizer\Tests\PhpAnalyzer\Model;

use Scrutinizer\PhpAnalyzer\Model\FileCollection;

class FileCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateCollection()
    {
        $col = FileCollection::createFromDirectory(__DIR__.'/Fixture/DirWithSomeFiles');
        $this->assertCount(3, $col);

        $files = $col->all();
        $this->assertArrayHasKey('A.php', $files);
        $this->assertArrayHasKey('Sub-Dir/B.php', $files);
        $this->assertArrayHasKey('text.txt', $files);
    }

    public function testCreateCollectionWithPattern()
    {
        $col = FileCollection::createFromDirectory(__DIR__.'/Fixture/DirWithSomeFiles', '*.php');
        $this->assertCount(2, $col);

        $files = $col->all();
        $this->assertArrayHasKey('A.php', $files);
        $this->assertArrayHasKey('Sub-Dir/B.php', $files);
    }

    public function testCreateCollectionWithFilter()
    {
        $col = FileCollection::createFromDirectory(__DIR__.'/Fixture/DirWithSomeFiles', null, array('paths' => 'Sub-Dir/*'));
        $this->assertCount(1, $col);
        $this->assertArrayHasKey('Sub-Dir/B.php', $col->all());

        $col = FileCollection::createFromDirectory(__DIR__.'/Fixture', null, array('paths' => 'DirWithSomeFiles/*', 'excluded_paths' => array('*.php')));
        $this->assertCount(1, $col);
        $this->assertArrayHasKey('DirWithSomeFiles/text.txt', $col->all());
    }

    public function testCreateFromZipFile()
    {
        $col = FileCollection::createFromZipFile(__DIR__.'/Fixture/zip-file.zip');
        $col2 = FileCollection::createFromDirectory(__DIR__.'/Fixture/DirWithSomeFiles');

        $this->assertEquals($col2, $col);
    }

    public function testCreateFromZipFileWithPattern()
    {
        $col = FileCollection::createFromZipFile(__DIR__.'/Fixture/zip-file.zip', '*.php');
        $col2 = FileCollection::createFromDirectory(__DIR__.'/Fixture/DirWithSomeFiles', '*.php');

        $this->assertEquals($col2, $col);
    }

    public function testFilterCollection()
    {
        $col = FileCollection::createFromDirectory(__DIR__.'/Fixture/DirWithSomeFiles', '*.php');
        $filteredCol = $col->filter('A.php');

        $this->assertNotSame($col, $filteredCol);
        $this->assertCount(1, $filteredCol);

        $files = $filteredCol->all();
        $this->assertArrayHasKey('A.php', $files);
    }

    public function testMergeCollections()
    {
        $col = FileCollection::createFromZipFile(__DIR__.'/Fixture/zip-file.zip');
        $col2 = FileCollection::createFromDirectory(__DIR__.'/Fixture/DirWithSomeFiles');

        $merged = $col->merge($col2);

        $this->assertCount(3, $col);
        $files = $col->all();

        $this->assertArrayHasKey('A.php', $files);
        $this->assertArrayHasKey('Sub-Dir/B.php', $files);
        $this->assertArrayHasKey('text.txt', $files);
    }
}
