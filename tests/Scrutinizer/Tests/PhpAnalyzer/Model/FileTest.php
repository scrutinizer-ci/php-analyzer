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

use Scrutinizer\PhpAnalyzer\Model\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateConvertsParserErrorToComment()
    {
        $file = File::create('missing_semicolon.php', '<?php echo "foo"');
        $this->assertInstanceOf('PHPParser_Node', $file->getAst());

        $comments = $file->getComments(1);
        $this->assertSame(1, count($comments));
        $this->assertContains('This code did not parse for me. Apparently, there is an error somewhere around this line', (string) $comments[0]);
    }
}