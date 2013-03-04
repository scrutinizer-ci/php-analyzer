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

namespace Scrutinizer\PhpAnalyzer\ArgumentChecker;

use Scrutinizer\PhpAnalyzer\Model\Parameter;

/**
 * Default checker which uses the basic semantics of the PHP language.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class DefaultArgumentChecker extends ChainableArgumentChecker
{
    public function getMissingArguments(\Scrutinizer\PhpAnalyzer\Model\AbstractFunction $function, array $args, \Scrutinizer\PhpAnalyzer\Model\MethodContainer $clazz = null)
    {
        if ($function->hasVariableParameters()) {
            return $this->nextGetMissingArguments($function, $args);
        }

        $missingArgs = array();
        foreach ($function->getParameters() as $param) {
            /** @var $param Parameter */

            if ( ! isset($args[$index = $param->getIndex()]) && ! $param->isOptional()) {
                $missingArgs[$index] = $param->getName();
            }
        }

        return $missingArgs;
    }

    public function getMismatchedArgumentTypes(\Scrutinizer\PhpAnalyzer\Model\AbstractFunction $function, array $argTypes, \Scrutinizer\PhpAnalyzer\Model\MethodContainer $clazz = null)
    {
        if ($function->hasVariableParameters()) {
            return $this->nextGetMismatchedArgumentTypes($function, $argTypes);
        }

        $mismatchedTypes = array();
        foreach ($function->getParameters() as $param) {
            /** @var $param Parameter */

            $index = $param->getIndex();

            if ( ! isset($argTypes[$index])) {
                continue;
            }

            if ( ! $this->typeChecker->mayBePassed($param->getPhpType(), $argTypes[$index])) {
                $mismatchedTypes[$index] = $param->getPhpType();
            }
        }

        return $mismatchedTypes;
    }
}