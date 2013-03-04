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
use Scrutinizer\PhpAnalyzer\Model\FileCollection;

class RepeatedPass implements AnalyzerAwareInterface, RepeatedPassAwareInterface
{
    const MAX_RUNS = 10;

    private $repeat = false;
    private $passes;
    private $analyzer;
    private $repeatedPass;
    private $maxRuns;

    /**
     * @param array<AnalysisPassInterface|RepeatedPass> $passes
     * @param integer $maxRuns
     */
    public function __construct(array $passes, $maxRuns = self::MAX_RUNS)
    {
        if ( ! $passes) {
            throw new \InvalidArgumentException('$passes cannot be empty.');
        }

        foreach ($passes as $pass) {
            if ($pass instanceof ConfigurablePassInterface) {
                throw new \InvalidArgumentException('Configurable passes are not supported.');
            }

            if ($pass instanceof RepeatedPassAwareInterface) {
                $pass->setRepeatedPass($this);
            }
        }

        $this->passes = $passes;
        $this->maxRuns = (integer) $maxRuns;
    }

    public function setRepeatedPass(RepeatedPass $repeatedPass)
    {
        $this->repeatedPass = $repeatedPass;
    }

    public function setAnalyzer(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;

        foreach ($this->passes as $pass) {
            if ($pass instanceof AnalyzerAwareInterface) {
                $pass->setAnalyzer($analyzer);
            }
        }
    }

    public function analyze(FileCollection $files)
    {
        $runs = 0;
        do {
            $runs += 1;
            $this->repeat = false;
            foreach ($this->passes as $pass) {
                $this->analyzer->logger->debug(sprintf('Running pass "%s"...', get_class($pass)));

                // Nested repeated passes are also treated specially.
                if ($pass instanceof RepeatedPass) {
                    $pass->analyze($files);

                    continue;
                }

                if ($pass instanceof CallbackAnalysisPassInterface) {
                    $pass->beforeAnalysis();
                }

                foreach ($files as $file) {
                    $this->analyzer->logger->debug(sprintf('Analyzing file "%s"...', $file->getName()));
                    if ($file instanceof AnalyzerAwareInterface) {
                        $file->setAnalyzer($this->analyzer);
                    }

                    $pass->analyze($file);
                }

                if ($pass instanceof CallbackAnalysisPassInterface) {
                    $pass->afterAnalysis();
                }
            }
        } while ($this->repeat && $runs < $this->maxRuns);
    }

    public function repeat()
    {
        if ( ! $this->repeat) {
            $this->analyzer->logger->debug('Repeatable passes will be repeated.');
        }

        $this->repeat = true;

        if (null !== $this->repeatedPass) {
            $this->repeatedPass->repeat();
        }
    }
}