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

interface ContainerMethodInterface
{
    /**
     * @return Method
     */
    function getMethod();

    /**
     * @return string
     */
    function getDeclaringClass();

    /**
     * @return string
     */
    function getName();

    /**
     * Returns a qualified name for this method of the format "FQCN::methodName".
     *
     * @return string
     */
    function getQualifiedName();

    /**
     * @return boolean
     */
    function isInherited();

    /**
     * The container that this method was called on.
     *
     * This is not necessary equal to the container that this method is
     * actually declared in.
     *
     * ```php
     * class A { public function a() { } }
     * class B extends A { }
     * $b = new B();
     * $b->a(); // For this call object<B> would be returned.
     * ```
     *
     * @return MethodContainer
     */
    function getContainer();
}