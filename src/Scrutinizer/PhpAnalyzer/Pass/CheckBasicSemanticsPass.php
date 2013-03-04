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

namespace Scrutinizer\PhpAnalyzer\Pass;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Scrutinizer\PhpAnalyzer\Config\NodeBuilder;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypedScopeCreator;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\Model\Comment;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\ClassMethod;

/**
 * Basic Semantic Checks
 *
 * This pass checks basic PHP semantics such as whether you have defined properties on an interface, or that
 * classes with abstract methods must be declared abstract. Most of the errors catched by this pass would lead to
 * fatal PHP runtime errors.
 *
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class CheckBasicSemanticsPass extends AstAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    public function getConfiguration()
    {
        $tb = new TreeBuilder();
        $tb->root('basic_semantic_checks', 'array', new NodeBuilder())
            ->canBeDisabled();

        return $tb;
    }

    protected function isEnabled()
    {
        return $this->getSetting('enabled');
    }

    public function visit(NodeTraversal $t, \PHPParser_Node $n, \PHPParser_Node $parent = null)
    {
        if ($n instanceof \PHPParser_Node_Stmt_Property) {
            $containerNode = NodeUtil::getContainer($n)->get();
            $class = $this->typeRegistry->getClassByNode($containerNode);

            if ($class->isInterface()) {
                $this->phpFile->addComment($n->getLine(), Comment::error(
                    'basic_semantics.property_on_interface',
                    'In PHP, declaring a method on an interface is not yet allowed.'
                ));
            }
        }

        if (NodeUtil::isMethodContainer($n)) {
            $class = $this->typeRegistry->getClassByNode($n);

            if ($class instanceof Clazz && ! $class->isAbstract()) {
                $abstractMethods = array();

                foreach ($class->getMethods() as $method) {
                    assert($method instanceof ClassMethod);
                    /** @var $method ClassMethod */

                    if ($method->isAbstract()) {
                        $abstractMethods[] = $method->getMethod()->getName();
                    }
                }

                if ( ! empty($abstractMethods)) {
                    switch (count($abstractMethods)) {
                        case 1:
                            $this->phpFile->addComment($n->getLine(), Comment::error(
                                'basic_semantics.abstract_method_on_non_abstract_class',
                                'There is one abstract method ``%method%`` in this class; you could implement it, or declare this class as abstract.',
                                array('method' => reset($abstractMethods))
                            ));
                            break;

                        default:
                            $this->phpFile->addComment($n->getLine(), Comment::error(
                                'basic_semantics.abstract_methods_on_non_abstract_class',
                                'There is at least one abstract method in this class. Maybe declare it as abstract, or implement the remaining methods: %methods%',
                                array('methods' => implode(', ', $abstractMethods))
                            ));
                    }
                }
            }

        }
    }
}