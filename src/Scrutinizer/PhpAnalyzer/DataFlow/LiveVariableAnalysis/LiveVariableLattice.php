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

namespace Scrutinizer\PhpAnalyzer\DataFlow\LiveVariableAnalysis;

use Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Variable;

class LiveVariableLattice implements LatticeElementInterface
{
    public $liveSet;

    public function __construct($numVars)
    {
        $this->liveSet = new BitSet($numVars);
    }

    public function equals(LatticeElementInterface $that)
    {
        if (!$that instanceof LiveVariableLattice) {
            return false;
        }

        return $this->liveSet->equals($that->liveSet);
    }

    public function isLive($varOrIndex)
    {
        if ($varOrIndex instanceof Variable) {
            $varOrIndex = $varOrIndex->getIndex();
        }

        assert('is_integer($varOrIndex)');

        return 1 === $this->liveSet->get($varOrIndex);
    }

    public function __clone()
    {
        $this->liveSet = clone $this->liveSet;
    }
}