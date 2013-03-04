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
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\SyntacticScopeCreator;
use Scrutinizer\PhpAnalyzer\PhpParser\Traversal\AbstractScopedCallback;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\Model\File;
use Scrutinizer\PhpAnalyzer\Model\PhpFile;

abstract class AstAnalyzerPass extends AbstractScopedCallback implements AnalysisPassInterface, AnalyzerAwareInterface
{
    protected static $prettyPrinter;

    /** @var PhpFile */
    protected $phpFile;

    /** @var TypeRegistry */
    protected $typeRegistry;

    /** @var Analyzer */
    protected $analyzer;

    public function __construct()
    {
        if (null === self::$prettyPrinter) {
            self::$prettyPrinter = new \PHPParser_PrettyPrinter_Zend();
        }
    }

    public function setAnalyzer(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
        $this->typeRegistry = $analyzer->getTypeRegistry();
    }

    public function analyze(File $file)
    {
        if ( ! $this->isEnabled()) {
            return;
        }

        if (!$file instanceof PhpFile) {
            return;
        }

        $this->phpFile = $file;
        NodeTraversal::traverseWithCallback($file->getAst(), $this, $this->getScopeCreator());
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

    protected function getScopeCreator()
    {
        return new SyntacticScopeCreator();
    }
}