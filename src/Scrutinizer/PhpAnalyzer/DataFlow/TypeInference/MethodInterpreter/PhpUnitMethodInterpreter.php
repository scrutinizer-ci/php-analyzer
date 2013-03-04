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

namespace Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\MethodInterpreter;

class PhpUnitMethodInterpreter implements MethodInterpreterInterface
{
    private $registry;

    public function __construct(\Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getPreciserMethodReturnTypeKnowingArguments(\Scrutinizer\PhpAnalyzer\Model\ContainerMethodInterface $method, array $argValues, array $argTypes)
    {
        $container = $method->getContainer();
        if ($container->isSubTypeOf($this->registry->getClassOrCreate('PHPUnit_Framework_TestCase'))) {
            switch (strtolower($method->getName())) {
                case 'getmock':
                    return $this->registry->getNativeType('unknown');
            }
        }

        if ($container->isSubTypeOf($this->registry->getClassOrCreate('PHPUnit_Framework_MockObject_MockBuilder'))) {
            switch (strtolower($method->getName())) {
                case 'getmock':
                    return $this->registry->getNativeType('unknown');
            }
        }

        return null;
    }
}