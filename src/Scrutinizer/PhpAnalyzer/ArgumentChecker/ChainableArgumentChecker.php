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

/**
 * Allows to chain several checkers.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
abstract class ChainableArgumentChecker implements ArgumentCheckerInterface
{
    protected $registry;
    protected $typeChecker;

    /** @var ChainableArgumentChecker */
    private $first;

    /** @var ChainableArgumentChecker */
    private $next;

    public function __construct(\Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry $registry, \Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeChecker $typeChecker)
    {
        $this->registry = $registry;
        $this->typeChecker = $typeChecker;
        $this->first = $this;
    }

    public final function append(ChainableArgumentChecker $checker)
    {
        if (null !== $this->next) {
            $this->next->append($checker);

            return;
        }

        $this->next = $checker;
        $checker->first = $this;
    }

    protected final function first()
    {
        return $this->first;
    }

    protected final function nextGetMissingArguments(\Scrutinizer\PhpAnalyzer\Model\AbstractFunction $function, array $args, \Scrutinizer\PhpAnalyzer\Model\MethodContainer $clazz = null)
    {
        if (null === $this->next) {
            return array();
        }

        return $this->next->getMissingArguments($function, $args, $clazz);
    }

    protected final function nextGetMismatchedArgumentTypes(\Scrutinizer\PhpAnalyzer\Model\AbstractFunction $function, array $types, \Scrutinizer\PhpAnalyzer\Model\MethodContainer $clazz = null)
    {
        if (null === $this->next) {
            return array();
        }

        return $this->next->getMismatchedArgumentTypes($function, $types, $clazz);
    }
}