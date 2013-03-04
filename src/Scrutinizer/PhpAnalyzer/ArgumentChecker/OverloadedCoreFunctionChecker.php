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

use Scrutinizer\PhpAnalyzer\Model\AbstractFunction;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\MethodContainer;

/**
 * Checker which is aware of the behavior of some of PHP's core functions.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class OverloadedCoreFunctionChecker extends ChainableArgumentChecker
{
    public function getMissingArguments(AbstractFunction $function, array $args, MethodContainer $clazz = null)
    {
        if ( ! $function instanceof GlobalFunction) {
            return $this->nextGetMissingArguments($function, $args, $clazz);
        }

        switch ($function->getName()) {
            case 'array_udiff_assoc':
            case 'array_udiff':
            case 'array_uintersect_assoc':
            case 'array_uintersect':
                return array_slice(array('array1', 'array2', 'data_compare_func'), count($args));

            case 'array_udiff_uassoc':
            case 'array_uintersect_uassoc':
                return array_slice(array('array1', 'array2', 'data_compare_func', 'key_compare_func'), count($args));

            default:
                return $this->nextGetMissingArguments($function, $args, $clazz);
        }
    }

    public function getMismatchedArgumentTypes(AbstractFunction $function, array $argTypes, MethodContainer $clazz = null)
    {
        if ( ! $function instanceof GlobalFunction) {
            return $this->nextGetMismatchedArgumentTypes($function, $argTypes, $clazz);
        }

        $expectedTypes = array();
        switch (strtolower($function->getName())) {
            case 'array_udiff_assoc':
            case 'array_udiff':
            case 'array_uintersect_assoc':
            case 'array_uintersect':
                // If less than three arguments are passed, then there is some parameter
                // missing. An error for this has already been raised, so just bail out here.
                if (count($argTypes) < 3) {
                    return array();
                }

                $expectedTypes = array_fill(0, count($argTypes) - 1, $this->registry->getNativeType('array'));
                $expectedTypes[] = $this->registry->getNativeType('callable');
                break;

            case 'array_udiff_uassoc':
            case 'array_uintersect_uassoc':
                // Same as above, an error has already been raised in this case.
                if (count($argTypes) < 4) {
                    return array();
                }

                $expectedTypes = array_fill(0, count($argTypes) - 2, $this->registry->getNativeType('array'));
                $expectedTypes[] = $this->registry->getNativeType('callable');
                $expectedTypes[] = $this->registry->getNativeType('callable');
                break;

            default:
                return $this->nextGetMismatchedArgumentTypes($function, $argTypes, $clazz);
        }

        $mismatchedTypes = array();
        foreach ($expectedTypes as $i => $expectedType) {
            if ( ! isset($argTypes[$i])) {
                throw new \LogicException(sprintf('There is no actual type at index "%d".', $i));
            }

            if ( ! $this->typeChecker->mayBePassed($expectedType, $argTypes[$i])) {
                $mismatchedTypes[$i] = $expectedType;
            }
        }

        return $mismatchedTypes;
    }
}