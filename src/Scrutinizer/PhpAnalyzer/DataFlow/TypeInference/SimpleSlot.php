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

namespace Scrutinizer\PhpAnalyzer\DataFlow\TypeInference;

use Scrutinizer\PhpAnalyzer\PhpParser\Scope\StaticSlotInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;

class SimpleSlot implements StaticSlotInterface
{
    private $name;
    private $type;
    private $typeInferred;
    private $reference = false;

    public function __construct($name, PhpType $type, $typeInferred)
    {
        $this->name = $name;
        $this->type = $type;
        $this->typeInferred = $typeInferred;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function isTypeInferred()
    {
        return $this->typeInferred;
    }

    public function isReference()
    {
        return $this->reference;
    }

    public function setReference($bool)
    {
        $this->reference = (boolean) $bool;
    }
}