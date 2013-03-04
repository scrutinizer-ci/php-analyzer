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

namespace Scrutinizer\PhpAnalyzer\PhpParser\Traversal;

use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;

/**
 * Extends the callback interface to be aware of scopes.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
interface ScopedCallbackInterface extends CallbackInterface
{
    /**
     * Called immediately after entering a new scope.
     *
     * The new scope can be accessed through $traversal->getScope().
     *
     * @return void
     */
    function enterScope(NodeTraversal $traversal);

    /**
     * Called immediately before exiting a scope.
     *
     * The ending scope can be accessed through $traversal->getScope().
     *
     * @return void.
     */
    function exitScope(NodeTraversal $traversal);
}