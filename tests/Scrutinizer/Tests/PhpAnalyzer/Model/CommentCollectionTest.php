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

class CommentCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $col = new \Scrutinizer\PhpAnalyzer\Model\CommentCollection();

        $a = \Scrutinizer\PhpAnalyzer\Model\Comment::error('foo', 'bar');
        $b = \Scrutinizer\PhpAnalyzer\Model\Comment::error('foo', 'bar');

        $col->add(1, $a);
        $col->add(1, $b);
        $this->assertCount(1, $col->all(1));
    }
}