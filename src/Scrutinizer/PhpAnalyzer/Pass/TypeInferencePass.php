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

use Psr\Log\LoggerInterface;
use Scrutinizer\PhpAnalyzer\Pass\AnalysisPassInterface;
use Scrutinizer\PhpAnalyzer\Analyzer;
use Scrutinizer\PhpAnalyzer\AnalyzerAwareInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\PhpParser\Traversal\AbstractScopedCallback;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\FunctionInterpreter\ArrayFunctionInterpreter;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\FunctionInterpreter\ChainableFunctionInterpreter;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\FunctionInterpreter\CoreFunctionInterpreter;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\MethodInterpreter\ChainableMethodInterpreter;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\MethodInterpreter\CoreMethodInterpreter;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\MethodInterpreter\PhpUnitMethodInterpreter;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\ReverseInterpreter\SemanticReverseAbstractInterpreter;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypeInference;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypedScopeCreator;
use Scrutinizer\PhpAnalyzer\Exception\MaxIterationsExceededException;
use Scrutinizer\PhpAnalyzer\Model\File;
use Scrutinizer\PhpAnalyzer\Model\PhpFile;

/**
 * Compiler Pass for running type inference analysis.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class TypeInferencePass extends AbstractScopedCallback implements AnalysisPassInterface, AnalyzerAwareInterface
{
    /** @var LoggerInterface */
    private $logger;
    private $registry;
    private $reverseInterpreter;
    private $functionInterpreter;
    private $methodInterpreter;
    private $commentParser;
    private $scopeCreator;

    /** @var File */
    private $file;

    public function setAnalyzer(Analyzer $analyzer)
    {
        $this->logger = $analyzer->logger;
        $this->registry = $registry = $analyzer->getTypeRegistry();
        $this->reverseInterpreter = new SemanticReverseAbstractInterpreter($registry);
        $this->functionInterpreter = new ChainableFunctionInterpreter(array(
            new CoreFunctionInterpreter($registry),
            new ArrayFunctionInterpreter($registry),
        ));
        $this->methodInterpreter = new ChainableMethodInterpreter(array(
            new CoreMethodInterpreter($registry),
            new PhpUnitMethodInterpreter($registry),
        ));
        $this->commentParser = new \Scrutinizer\PhpAnalyzer\PhpParser\DocCommentParser($registry);
        $this->commentParser->setLogger($analyzer->logger);
        $this->scopeCreator = new TypedScopeCreator($registry);
    }

    public function getScopeCreator()
    {
        return $this->scopeCreator;
    }

    public function analyze(File $file)
    {
        if (!$file instanceof PhpFile) {
            return;
        }

        $this->file = $file;
        NodeTraversal::traverseWithCallback($file->getAst(), $this, $this->scopeCreator);
    }

    public function enterScope(NodeTraversal $traversal)
    {
        $typeInference = new TypeInference($traversal->getControlFlowGraph(),
                                           $this->reverseInterpreter,
                                           $this->functionInterpreter,
                                           $this->methodInterpreter,
                                           $this->commentParser,
                                           $traversal->getScope(),
                                           $this->registry,
                                           $this->logger);
        try {
            $typeInference->analyze();
        } catch (MaxIterationsExceededException $ex) {
            $scopeRoot = $traversal->getScopeRoot();
            $this->logger->warning($ex->getMessage().' - Scope-Root: '.get_class($scopeRoot). ' on line '.$scopeRoot->getLine().' in '.$this->file->getName());
        }
    }
}