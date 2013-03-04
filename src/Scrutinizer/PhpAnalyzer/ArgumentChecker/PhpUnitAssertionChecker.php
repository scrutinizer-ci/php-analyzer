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
use Scrutinizer\PhpAnalyzer\Model\Method;
use Scrutinizer\PhpAnalyzer\Model\MethodContainer;

/**
 * PhpUnit Assertion Checker
 *
 * This checker basically skips all arguments checks for any of PHPUnit's
 * assertion functions. There are two reasons for skipping assertions:
 *
 * 1. Currently, we perform no reverse interpretation of assertion functions. As
 *    a result, we frequently get false-positives in tests.
 *
 * 2. Assertion arguments do no need to be checked anyway, as they should be
 *    executed on each run of the test suite and their calls should be save
 *    except from maybe a few edge cases which are negligible for now.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class PhpUnitAssertionChecker extends ChainableArgumentChecker
{
    public function getMissingArguments(AbstractFunction $function, array $args, MethodContainer $clazz = null)
    {
        return $this->nextGetMissingArguments($function, $args, $clazz);
    }

    public function getMismatchedArgumentTypes(AbstractFunction $function, array $argTypes, MethodContainer $clazz = null)
    {
        if (null === $clazz || ! $function instanceof Method) {
            return $this->nextGetMismatchedArgumentTypes($function, $argTypes, $clazz);
        }

        if ( ! $clazz->isSubtypeOf($this->registry->getClassOrCreate('PHPUnit_Framework_TestCase'))) {
            return $this->nextGetMismatchedArgumentTypes($function, $argTypes, $clazz);
        }

        if (0 !== stripos($function->getName(), 'assert')) {
            return $this->nextGetMismatchedArgumentTypes($function, $argTypes, $clazz);
        }

        return array();
    }
}