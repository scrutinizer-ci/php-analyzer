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

/**
 * Interface for type visitors.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
interface VisitorInterface
{
    /**
     * @return void
     */
    public function visitNoType();

    /**
     * @return void
     */
    public function visitAllType();

    /**
     * @return void
     */
    public function visitBooleanType();

    /**
     * @return void
     */
    public function visitNoObjectType();

    /**
     * @return void
     */
    public function visitObjectType(ObjectType $type);

    /**
     * @return void
     */
    public function visitUnknownType();

    /**
     * @return void
     */
    public function visitNullType();

    /**
     * @return void
     */
    public function visitIntegerType();

    /**
     * @return void
     */
    public function visitDoubleType();

    /**
     * @return void
     */
    public function visitStringType();

    /**
     * @return void
     */
    public function visitUnionType(UnionType $type);

    /**
     * @return void
     */
    public function visitArrayType(ArrayType $type);

    /**
     * @return void
     */
    public function visitCallableType(CallableType $type);

    /**
     * @return void
     */
    public function visitResourceType(ResourceType $type);
}