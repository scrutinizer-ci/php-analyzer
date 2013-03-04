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

use JMS\PhpManipulator\TokenStream;
use Scrutinizer\PhpAnalyzer\Model\File;
use Scrutinizer\PhpAnalyzer\Model\PhpFile;

abstract class SuccessiveTokenAndAstAnalzyer extends AstAnalyzerPass
{
    /** @var TokenStream */
    protected $stream;

    public function __construct()
    {
        parent::__construct();

        $this->stream = new TokenStream();
    }

    public function analyze(File $file)
    {
        if (!$file instanceof PhpFile) {
            return;
        }

        $this->phpFile = $file;

        if ($this->analyzeAstFirst()) {
	        parent::analyze($file);

	        $this->stream->setCode($file->getContent());
	        $this->analyzeStream();
        } else {
        	$this->stream->setCode($file->getContent());
        	$this->analyzeStream();

        	parent::analyze($file);
        }
    }

    protected function analyzeAstFirst()
    {
    	return true;
    }

    abstract protected function analyzeStream();
}