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
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\Traversal\AbstractScopedCallback;
use Scrutinizer\PhpAnalyzer\DataFlow\VariableReachability\MayBeReachingUseAnalysis;
use Scrutinizer\PhpAnalyzer\DataFlow\VariableReachability\MustBeReachingDefAnalysis;
use Scrutinizer\PhpAnalyzer\Model\File;
use Scrutinizer\PhpAnalyzer\Model\PhpFile;

class VariableReachabilityPass extends AbstractScopedCallback implements AnalysisPassInterface, \Scrutinizer\PhpAnalyzer\AnalyzerAwareInterface
{
    private $logger;

    public function setAnalyzer(\Scrutinizer\PhpAnalyzer\Analyzer $analyzer)
    {
        $this->logger = $analyzer->logger;
    }

    public function analyze(File $file)
    {
        if ( ! $file instanceof PhpFile) {
            return;
        }

        NodeTraversal::traverseWithCallback($file->getAst(), $this);
    }

    public function enterScope(NodeTraversal $t)
    {
        $cfg = $t->getControlFlowGraph();
        $scope = $t->getScope();

        $mayBeAnalysis = new MayBeReachingUseAnalysis($cfg, $scope);
        $mayBeAnalysis->analyze();

        $mustBeAnalysis = new MustBeReachingDefAnalysis($cfg, $scope);
        $mustBeAnalysis->setLogger($this->logger);
        $mustBeAnalysis->analyze();
    }
}