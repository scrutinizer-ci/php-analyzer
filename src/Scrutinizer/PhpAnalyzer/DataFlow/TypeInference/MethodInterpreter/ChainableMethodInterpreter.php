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

namespace Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\MethodInterpreter;

class ChainableMethodInterpreter implements \Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\MethodInterpreter\MethodInterpreterInterface
{
    private $interpreters;

    /**
     * @param array<MethodInterpreterInterface> $interpreters
     */
    public function __construct(array $interpreters)
    {
        $this->interpreters = $interpreters;
    }

    public function getPreciserMethodReturnTypeKnowingArguments(\Scrutinizer\PhpAnalyzer\Model\ContainerMethodInterface $method, array $argValues, array $argTypes)
    {
        foreach ($this->interpreters as $interpreter) {
            if (null !== $restrictedType = $interpreter->getPreciserMethodReturnTypeKnowingArguments($method, $argValues, $argTypes)) {
                return $restrictedType;
            }
        }

        return null;
    }
}