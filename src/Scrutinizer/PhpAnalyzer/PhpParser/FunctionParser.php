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

namespace Scrutinizer\PhpAnalyzer\PhpParser;

use Scrutinizer\PhpAnalyzer\PhpParser\ParameterParser;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;

class FunctionParser
{
    private $typeRegistry;
    private $paramParser;
    private $importedNamespaces = array();

    public function __construct(TypeRegistry $registry, ParameterParser $paramParser)
    {
        $this->typeRegistry = $registry;
        $this->paramParser = $paramParser;
    }

    public function setImportedNamespaces(array $namespaces)
    {
        $this->importedNamespaces = $namespaces;
    }

    public function parse(\PHPParser_Node_Stmt_Function $node)
    {
        $function = new GlobalFunction(implode("\\", $node->namespacedName->parts));
        $function->setImportedNamespaces($this->importedNamespaces);
        $function->setAstNode($node);
        $function->setReturnByRef($node->byRef);
        $function->setVariableParameters(false !== strpos($node->getDocComment(), '@jms-variable-parameters'));

        foreach ($this->paramParser->parse($node->params, 'Scrutinizer\PhpAnalyzer\Model\FunctionParameter') as $param) {
            $function->addParameter($param);
        }

        return $function;
    }
}