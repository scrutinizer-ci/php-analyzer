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

use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\Model\AbstractFunction;
use Scrutinizer\PhpAnalyzer\Model\MethodContainer;

/**
 * Interface for method/function call checkers.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
interface ArgumentCheckerInterface
{
    /**
     * Returns the missing arguments for the given function/method call.
     *
     * @param AbstractFunction $function The function/method that is being called.
     * @param \PHPParser_Node_Arg[] $args The given arguments.
     *
     * @return string[] The names of the parameters that are missing.
     */
    public function getMissingArguments(AbstractFunction $function, array $args, MethodContainer $clazz = null);

    /**
     * Returns an associative array of parameter names mapped to expected types.
     *
     * @param AbstractFunction $function The called function/method.
     * @param PhpType[] $argTypes The passed types.
     *
     * @return array<string,PhpType>
     */
    public function getMismatchedArgumentTypes(AbstractFunction $function, array $argTypes, MethodContainer $clazz = null);
}