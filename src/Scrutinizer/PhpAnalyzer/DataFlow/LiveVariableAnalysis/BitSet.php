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

/**
 * Abstraction over a simple BitSet.
 *
 * This implementation is not complete, but only contains those methods that we need for the analysis.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class BitSet implements \Countable
{
    private $values = array();
    private $size = 0;
    private $value = 0;
    private $efficient = false;

    public function __construct($initialSize = 0)
    {
        $this->size = $initialSize;

        if ($initialSize <= 31) {
            $this->value = 0;
            $this->efficient = true;

            return;
        }

        for ($i=0; $i<$initialSize; $i++) {
            $this->values[] = 0;
        }
    }

    public function performOr(BitSet $that)
    {
        if ($this->efficient) {
            $this->value |= $that->value;

            return;
        }

        for ($i=0,$c=count($this->values); $i<$c; $i++) {
            $this->values[$i] = $this->values[$i] | $that->values[$i];
        }
    }

    public function set($index)
    {
        if ($this->efficient) {
            $this->value |= 1 << $index;

            return;
        }

        $this->values[$index] = 1;
    }

    public function get($index)
    {
        if ($this->efficient) {
            return ($this->value >> $index) & 1;
        }

        return $this->values[$index];
    }

    public function __toString()
    {
        if ($this->efficient) {
            $str = '';
            $value = $this->value;
            for ($i=0;$i<$this->size;$i++) {
                $str .= ($value & 1) ? '1' : '0';
                $value >>= 1;
            }

            return $str;
        }

        $maxIndex = max(array_keys($this->values));
        $str = '';
        for ($i = 0; $i<=$maxIndex; $i++) {
            if (isset($this->values[$i]) && 1 === $this->values[$i]) {
                $str .= '1';
            } else {
                $str .= '0';
            }
        }

        return $str;
    }

    public function andNot(BitSet $that)
    {
        assert('count($this) === count($that)');

        if ($this->efficient) {
            $this->value &= ~$that->value;

            return;
        }

        for ($i=0,$c=count($this->values); $i<$c; $i++) {
            $this->values[$i] = $this->values[$i] & ~$that->values[$i];
        }
    }

    public function equals(BitSet $that)
    {
        if ($this->size !== $that->size) {
            return false;
        }

        if ($this->efficient) {
            return $this->value === $that->value;
        }

        for ($i=0; $i<$this->size; $i++) {
            if ($this->values[$i] !== $that->values[$i]) {
                return false;
            }
        }

        return true;
    }

    public function count()
    {
        return $this->size;
    }
}