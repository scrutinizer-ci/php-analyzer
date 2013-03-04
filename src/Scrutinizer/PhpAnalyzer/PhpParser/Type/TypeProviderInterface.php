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

namespace Scrutinizer\PhpAnalyzer\PhpParser\Type;

use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\GlobalConstant;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\InterfaceC;
use Scrutinizer\PhpAnalyzer\Model\TraitC;

/**
 * Interface for Type Providers.
 *
 * @author Johannes
 */
interface TypeProviderInterface
{
    /**
     * Batch loads the passed classes.
     *
     * If a class is not available, it simply is omitted from the resulting
     * array.
     *
     * @param array $names
     * @return array<Clazz|InterfaceC|TraitC> the keys must be the input names (case must be preserved)
     */
    function loadClasses(array $names);

    /**
     * Returns a class, or null.
     *
     * @param string $name
     * @return Clazz|InterfaceC|TraitC|null
     */
    function loadClass($name);

    /**
     * Returns a function, or null.
     *
     * @param string $name
     * @return GlobalFunction
     */
    function loadFunction($name);

    /**
     * Returns a constant, or null.
     *
     * @param string $name
     * @return GlobalConstant
     */
    function loadConstant($name);

    /**
     * Returns all classes that implement the given interface.
     *
     * @param string $name
     * @return array<Clazz>
     */
    function getImplementingClasses($name);
}