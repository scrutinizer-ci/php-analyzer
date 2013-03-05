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

use JMS\PhpManipulator\PhpParser\BlockNode;
use JMS\PhpManipulator\PhpParser\NormalizingNodeVisitor;
use Scrutinizer\Util\DiffUtils;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("ALL")
 */
class File
{
    private static $phpParser;

    /** @Serializer\Expose @Serializer\SerializedName("path") */
    private $name;

    private $content;
    private $comments;
    private $changedLines = array();
    private $diff;
    private $chunks;
    private $attributes = array();
    private $originalFile;
    private $fixedFile;

    public static function create($name, $content, $originalContent = null)
    {
        if (null === self::$phpParser) {
            self::initStatic();
        }

        if ('.php' === substr($name, -4)) {
            $file = self::createPhpFile($name, $content);
        } else {
            $file = new File($name, $content);
        }

        if (null !== $originalContent) {
            $file->setDiff(DiffUtils::generate($originalContent, $content));
        }

        return $file;
    }

    private static function createPhpFile($name, $content)
    {
        $file = new PhpFile($name, $content);

        try {
            $ast = self::$phpParser->parse(new \PHPParser_Lexer($content));
        } catch (\PHPParser_Error $parserEx) {
            // This at least allows to run all the passes. For those that
            // need an AST to work, they will obviously not do anything
            // useful, but maybe some can.
            // TODO: Implement some heuristics to attempt to fix the code.
            $ast = array(new BlockNode(array()));

            $lineNb = $parserEx->getRawLine();
            if (-1 === $lineNb) {
                // This is such a serious error that we at least need to
                // report it somehow even if the line is off.
                $file->addComment(1, Comment::error(
                        'unparsable_code',
                        "Cannot point out a specific line, but got the following parsing error when trying this code:\n\n%message%",
                        array('message' => $parserEx->getRawMessage())));
            } else {
                $file->addComment($lineNb, Comment::error(
                        'unparsable_code_with_line',
                        "This code did not parse for me. Apparently, there is an error somewhere around this line:\n\n%message%",
                        array('message' => $parserEx->getRawMessage())));
            }
        }

        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NameResolver());
        $traverser->addVisitor(new NormalizingNodeVisitor());
        $ast = $traverser->traverse($ast);

        // Wrap the AST in a block node if it has more than one root
        if (count($ast) > 1) {
            $ast = array(new BlockNode($ast));
        } else if (0 === count($ast)) {
            $ast = array(new BlockNode(array()));
        }

        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NodeConnector());

        // do not assign the AST here as the traverser clones nodes resulting
        // in different references for "parent", "next", "previous", etc.
        $traverser->traverse($ast);

        $file->setAst($ast[0]);

        return $file;
    }

    private static function initStatic()
    {
        self::$phpParser = new \PHPParser_Parser();
    }

    public function __construct($name, $code)
    {
        $this->name = $name;
        $this->content = $code;
        $this->comments = new CommentCollection();
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getName()
    {
        return $this->name;
    }

    public function hasFixedFile()
    {
        return null !== $this->fixedFile;
    }

    public function hasFixedFileWithChanges()
    {
        return null !== $this->fixedFile
                   && $this->fixedFile->getContent() !== $this->content;
    }

    public function getOrCreateFixedFile()
    {
        if (null !== $this->fixedFile) {
            return $this->fixedFile;
        }

        return $this->fixedFile = $this->createFixedFile();
    }

    protected function createFixedFile()
    {
        return new FixedFile($this->content);
    }

    public function getFixedFile()
    {
        return $this->fixedFile;
    }

    public function addComment($line, Comment $comment)
    {
        $this->comments->add($line, $comment);
    }

    public function hasComments()
    {
        return count($this->comments) > 0;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("comments")
     * @Serializer\XmlMap(entry="comment", keyAttribute="line")
     */
    public function getFlatComments()
    {
        return $this->comments->all();
    }

    public function getComments($line = null)
    {
        return $this->comments->all($line);
    }

    public function setComments(array $comments)
    {
        $this->comments->replace($comments);
    }

    public function setLineChanged($line)
    {
        $this->changedLines[$line] = true;
    }

    public function isLineChanged($line)
    {
        // always return true if no chunks have been set, and no lines
        // have been manually marked as changed
        if (null === $this->diff && !$this->changedLines) {
            return true;
        }

        return isset($this->changedLines[$line]);
    }

    public function setDiff($diff)
    {
        $this->diff = $diff;
        $this->chunks = DiffUtils::parse($diff);

        foreach ($this->chunks as $chunk) {
            $i = $chunk['new_start_index'];

            foreach ($chunk['diff'] as $change) {
                if ('removed' === $change['type']) {
                    continue;
                }

                if ('added' === $change['type']) {
                    $this->setLineChanged($i);
                }

                $i += 1;
            }
        }
    }

    public function getDiff()
    {
        return $this->diff;
    }

    public function getChunks()
    {
        return $this->chunks;
    }

    public function hasOriginalFile()
    {
        return null !== $this->diff;
    }

    public function getOriginalFile()
    {
        if (null === $this->diff) {
            throw new \LogicException('getOriginalFile() is only available after setDiff() has been called.');
        }

        if (null !== $this->originalFile) {
            return $this->originalFile;
        }

        if ($this->isNew()) {
            return self::create($this->name, '');
        }

        // TODO: What about files which have been renamed?
        return $this->originalFile = self::create($this->name, DiffUtils::reverseApply($this->content, $this->diff));
    }

    public function isNew()
    {
        if (null === $this->diff) {
            throw new \LogicException('isNew() is only available after setDiff() was called.');
        }

        return 1 === count($this->chunks) && $this->chunks[0]['original_start_index'] === 0
                    && $this->chunks[0]['original_size'] === 0;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    public function removeAttribute($key)
    {
        unset($this->attributes[$key]);
    }

    public function getAttribute($key, $default = null)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : $default;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @Serializer\VirtualProperty
     */
    public function getProposedPatch()
    {
        if (empty($this->fixedFile)) {
            return null;
        }

        $after = $this->fixedFile->getContent();
        if ($this->content === $after) {
            return null;
        }

        return DiffUtils::generate($this->content, $after);
    }
}
