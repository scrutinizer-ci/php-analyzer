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

namespace Scrutinizer\PhpAnalyzer\PhpParser;

use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;

class ParameterParser
{
    private $typeRegistry;

    public function __construct(TypeRegistry $registry)
    {
        $this->typeRegistry = $registry;
    }

    public function parse(array $params, $paramClass)
    {
        $result = array();
        $i = 0;
        foreach ($params as $paramNode) {
            assert($paramNode instanceof \PHPParser_Node_Param);

            $param = new $paramClass($paramNode->name, $i);
            $param->setAstNode($paramNode);
            $param->setPassedByRef($paramNode->byRef);

            if (null !== $paramNode->default) {
                $param->setOptional(true);
            }

            if ($type = $paramNode->getAttribute('type')) {
                $param->setPhpType($type);
            }

            $result[] = $param;
            $i += 1;
        }

        return $result;
    }
}