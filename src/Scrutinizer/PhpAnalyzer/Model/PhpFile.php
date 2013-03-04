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

use Scrutinizer\PhpAnalyzer\AnalyzerAwareInterface;
use JMS\Serializer\Annotation as Serializer;

/** @Serializer\ExclusionPolicy("ALL") */
class PhpFile extends File implements AnalyzerAwareInterface
{
    private $ast;
    private $classes = array();
    private $analyzer;

    public function setAnalyzer(\Scrutinizer\PhpAnalyzer\Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    public function setAst(\PHPParser_Node $ast)
    {
        $this->ast = $ast;
    }

    public function getAst()
    {
        return $this->ast;
    }

    public function addClass(\PHPParser_Node_Stmt_Class $class)
    {
        $this->classes[$class->name] = $class;
    }

    public function getClasses()
    {
        return $this->classes;
    }

    protected function createFixedFile()
    {
        return new FixedPhpFile($this->analyzer, $this->getContent());
    }
}