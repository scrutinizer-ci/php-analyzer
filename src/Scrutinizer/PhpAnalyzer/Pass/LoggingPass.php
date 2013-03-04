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

use Scrutinizer\PhpAnalyzer\Analyzer;
use Scrutinizer\PhpAnalyzer\AnalyzerAwareInterface;
use Scrutinizer\PhpAnalyzer\Model\File;

class LoggingPass implements AnalysisPassInterface, AnalyzerAwareInterface, CallbackAnalysisPassInterface
{
    private $message;
    private $logger;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function setAnalyzer(Analyzer $analyzer)
    {
        $this->logger = $analyzer->logger;
    }

    public function beforeAnalysis()
    {
        $this->logger->info($this->message);
    }

    public function analyze(File $file) { }
    public function afterAnalysis() { }
}