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

namespace Scrutinizer\PhpAnalyzer\PhpParser\Scope;

class MemoizedScopeCreator implements ScopeCreatorInterface
{
    private $delegate;
    private $scopes;

    public function __construct(ScopeCreatorInterface $delegate)
    {
        $this->delegate = $delegate;
        $this->scopes = new \SplObjectStorage();
    }

    public function createScope(\PHPParser_Node $node, Scope $parent = null)
    {
        if (isset($this->scopes[$node])) {
            return $this->scopes[$node];
        }

        return $this->scopes[$node] = $this->delegate->createScope($node, $parent);
    }
}