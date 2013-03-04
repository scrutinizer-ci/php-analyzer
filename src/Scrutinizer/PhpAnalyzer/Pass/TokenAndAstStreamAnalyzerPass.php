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

use Scrutinizer\PhpAnalyzer\Pass\AnalysisPassInterface;
use Scrutinizer\PhpAnalyzer\Analyzer;
use Scrutinizer\PhpAnalyzer\AnalyzerAwareInterface;
use JMS\PhpManipulator\SimultaneousTokenAstStream;
use Scrutinizer\PhpAnalyzer\Model\File;
use Scrutinizer\PhpAnalyzer\Model\PhpFile;

abstract class TokenAndAstStreamAnalyzerPass implements AnalysisPassInterface, AnalyzerAwareInterface
{
    protected static $prettyPrinter;

    protected $analyzer;
    protected $typeRegistry;
    protected $phpFile;
    protected $stream;

    public function __construct()
    {
        $this->stream = new SimultaneousTokenAstStream();

        if (null === self::$prettyPrinter) {
            self::$prettyPrinter = new \PHPParser_PrettyPrinter_Zend();
        }
    }

    public function setAnalyzer(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
        $this->typeRegistry = $analyzer->getTypeRegistry();
    }

    public final function analyze(File $file)
    {
        if (!$file instanceof PhpFile) {
            return;
        }

        $this->phpFile = $file;
        $this->stream->setInput($file->getContent(), $file->getAst());
        $this->analyzeStream();
    }

    abstract protected function analyzeStream();
}