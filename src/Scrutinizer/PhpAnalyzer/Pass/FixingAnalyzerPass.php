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

namespace Scrutinizer\PhpAnalyzer\Pass;

/**
 * Abstract base class for fixing passes.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
abstract class FixingAnalyzerPass implements \Scrutinizer\PhpAnalyzer\Pass\AnalysisPassInterface, \Scrutinizer\PhpAnalyzer\AnalyzerAwareInterface
{
    /** @var \JMS\PhpManipulator\SimultaneousTokenAstStream */
    protected $stream;

    /** @var \Scrutinizer\PhpAnalyzer\Analyzer */
    protected $analyzer;

    /** @var \Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry */
    protected $registry;

    /** @var \Scrutinizer\PhpAnalyzer\Model\FixedPhpFile */
    protected $fixedFile;

    public function __construct()
    {
        $this->stream = new \JMS\PhpManipulator\SimultaneousTokenAstStream();
        $tokenStream = $this->stream->getTokenStream();
        $tokenStream->setIgnoreComments(false);
        $tokenStream->setIgnoreWhitespace(false);
    }

    public function setAnalyzer(\Scrutinizer\PhpAnalyzer\Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
        $this->registry = $analyzer->getTypeRegistry();
    }

    public final function analyze(\Scrutinizer\PhpAnalyzer\Model\File $file)
    {
        if ( ! $file instanceof \Scrutinizer\PhpAnalyzer\Model\PhpFile) {
            return;
        }

        if ( ! $this->isEnabled()) {
            return;
        }

        $this->fixedFile = $fixedFile = $file->getOrCreateFixedFile();
        if ( ! $fixedFile->hasAst()) {
            return;
        }

        $this->stream->setInput($originalContent = $fixedFile->getContent(), $fixedFile->getAst());
        $this->analyzeStream();

        $newContent = $this->getNewContent();
        if ($newContent !== $originalContent) {
            $fixedFile->setContent($newContent);
        }
    }

    /**
     * Returns whether this pass is enabled.
     *
     * Allow sub-classes to easily disable this pass without having to implement
     * much logic themselves.
     *
     * @return boolean
     */
    protected function isEnabled()
    {
        return true;
    }

    protected function getNewContent()
    {
        $newContent = '';
        foreach ($this->stream->getTokenStream()->getTokens() as $token) {
            $newContent .= $token->getContent();
        }

        return $newContent;
    }

    abstract protected function analyzeStream();
}