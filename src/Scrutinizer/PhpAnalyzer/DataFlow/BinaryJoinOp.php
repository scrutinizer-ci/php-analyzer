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

namespace Scrutinizer\PhpAnalyzer\DataFlow;

/**
 * Breaks down all join operations into binary join operations.
 *
 * This will make implementing your join operation a bit easier as you just have to take care of a single case.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
final class BinaryJoinOp
{
    private $joinOp;

    public function __construct(callable $callable)
    {
        $this->joinOp = $callable;
    }

    public function __invoke(array $values)
    {
        switch ($nbValues = count($values)) {
            case 0:
                throw new \LogicException('$values must not be empty.');

            case 1:
                return $values[0];

            case 2:
                return call_user_func($this->joinOp, $values[0], $values[1]);

            default:
                // Finds the mid point of the list. This favors splitting the list into two lists of even length. For
                // example, a list with six elements is split into one list with two elements and another list with four
                // elements instead of two lists with three elements each.
                $mid = $nbValues >> 1;
                if ($nbValues > 4) {
                    $mid &= -2;
                }

                return call_user_func(
                    $this->joinOp,
                    $this->__invoke(array_slice($values, 0, $mid)),
                    $this->__invoke(array_slice($values, $mid))
                );
        }
    }
}