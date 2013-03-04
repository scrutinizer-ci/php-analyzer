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

namespace Scrutinizer\PhpAnalyzer\Model;

class CommentCollection implements \Countable
{
    private $comments = array();

    public function add($line, Comment $comment)
    {
        // Make sure that the same comment is not getting added twice to a line.
        // This might regularly be the case if the same error is found in two
        // different code elements which are placed on the same line.
        if (isset($this->comments[$line])) {
            foreach ($this->comments[$line] as $existingComment) {
                if ($existingComment->equals($comment)) {
                    return;
                }
            }
        }

        $this->comments[$line][] = $comment;
    }

    public function all($line = null)
    {
        if (null !== $line) {
            return isset($this->comments[$line]) ? $this->comments[$line] : array();
        }

        return $this->comments;
    }

    public function replace(array $comments)
    {
        $this->comments = $comments;
    }

    public function count()
    {
        return count($this->comments);
    }
}