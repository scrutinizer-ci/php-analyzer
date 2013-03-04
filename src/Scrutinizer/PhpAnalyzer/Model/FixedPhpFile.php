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

use Scrutinizer\PhpAnalyzer\Analyzer;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use JMS\PhpManipulator\PhpParser\ParseUtils;
use Scrutinizer\PhpAnalyzer\Pass\TypeInferencePass;

class FixedPhpFile extends FixedFile
{
    private $ast;
    private $typeInferencePass;

    public function __construct(Analyzer $analyzer, $content)
    {
        parent::__construct($content);

        $this->typeInferencePass = new TypeInferencePass();
        $this->typeInferencePass->setAnalyzer($analyzer);

        $this->updateAst();
    }

    public function getAst()
    {
        if (null === $this->ast) {
            throw new \LogicException('There is no AST. Possibly we could not parse the code, or this instance has just been unserialized. Please check with hasAst() prior to calling this method.');
        }

        return $this->ast;
    }

    public function hasAst()
    {
        return null !== $this->ast;
    }

    public function setContent($content)
    {
        parent::setContent($content);
        $this->updateAst();
    }

    private function updateAst()
    {
        try {
            $this->ast = ParseUtils::parse($this->getContent());

            NodeTraversal::traverseWithCallback($this->ast,
                                        $this->typeInferencePass,
                                        $this->typeInferencePass->getScopeCreator());

            NodeTraversal::traverseWithCallback($this->ast,
                                        $this->typeInferencePass,
                                        $this->typeInferencePass->getScopeCreator());
        } catch (\PHPParser_Error $ex) {
            // The original content of the file is not valid PHP code. We just ignore this.
            // Passes that require an AST, have to check with hasAst() manually.
        }
    }
}