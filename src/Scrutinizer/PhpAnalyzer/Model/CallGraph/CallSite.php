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

namespace Scrutinizer\PhpAnalyzer\Model\CallGraph;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Scrutinizer\PhpAnalyzer\Model\AbstractFunction;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\Method;

/**
 * @ORM\Entity(readOnly = true)
 * @ORM\Table(name = "call_sites")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name = "type", type = "string", length = 3)
 * @ORM\DiscriminatorMap({
 *     "f2f" = "Scrutinizer\PhpAnalyzer\Model\CallGraph\FunctionToFunctionCallSite",
 *     "f2m" = "Scrutinizer\PhpAnalyzer\Model\CallGraph\FunctionToMethodCallSite",
 *     "m2f" = "Scrutinizer\PhpAnalyzer\Model\CallGraph\MethodToFunctionCallSite",
 *     "m2m" = "Scrutinizer\PhpAnalyzer\Model\CallGraph\MethodToMethodCallSite",
 * })
 *
 * @author Johannes
 */
abstract class CallSite
{
    /** @ORM\Id @ORM\GeneratedValue(strategy = "AUTO") @ORM\Column(type = "integer") */
    private $id;

    /** @ORM\OneToMany(targetEntity = "Argument", mappedBy = "callSite", cascade = {"persist", "remove"}, orphanRemoval = true) */
    private $args;

    // NOT PERSISTED
    private $astNode;

    public static function create(AbstractFunction $source, AbstractFunction $target, array $args = array())
    {
        switch (true) {
            case $source instanceof GlobalFunction && $target instanceof GlobalFunction:
                return new FunctionToFunctionCallSite($source, $target, $args);

            case $source instanceof GlobalFunction && $target instanceof Method:
                return new FunctionToMethodCallSite($source, $target, $args);

            case $source instanceof Method && $target instanceof GlobalFunction:
                return new MethodToFunctionCallSite($source, $target, $args);

            case $source instanceof Method && $target instanceof Method:
                return new MethodToMethodCallSite($source, $target, $args);

            default:
                throw new \LogicException('Previous statements were exhaustive.');
        }
    }

    /**
     * @param array<Argument> $args
     */
    public function __construct(AbstractFunction $source, AbstractFunction $target, array $args = array())
    {
        $this->setSource($source);
        $this->setTarget($target);
        $source->addCallSite($this);
        $target->addCallSite($this);

        foreach ($args as $arg) {
            $arg->setCallSite($this);
        }

        $this->args = new ArrayCollection($args);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getArgs()
    {
        return $this->args;
    }

    public function getAstNode()
    {
        return $this->astNode;
    }

    public function setAstNode(\PHPParser_Node $node)
    {
        $this->astNode = $node;
    }

    /** @return Method|GlobalFunction */
    abstract public function getSource();

    /** @return Method|GlobalFunction */
    abstract public function getTarget();

    abstract protected function setSource(AbstractFunction $function);
    abstract protected function setTarget(AbstractFunction $function);
}