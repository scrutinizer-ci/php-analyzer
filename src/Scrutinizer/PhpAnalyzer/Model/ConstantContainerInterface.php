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

namespace Scrutinizer\PhpAnalyzer\Model;

interface ConstantContainerInterface
{
    /**
     * Returns all constants.
     *
     * @return ContainerConstantInterface[]
     */
    function getConstants();

    /**
     * Returns whether a constants exists
     *
     * @param string $name case-sensitive
     * @return boolean
     */
    function hasConstant($name);

    /**
     * Adds a constant.
     *
     * @param \Scrutinizer\PhpAnalyzer\Model\Constant $constant
     * @param string $declaringClass
     */
    function addConstant(Constant $constant, $declaringClass = null);
}