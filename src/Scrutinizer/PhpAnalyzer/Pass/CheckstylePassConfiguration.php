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

use Scrutinizer\PhpAnalyzer\Config\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\EnumNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class CheckstylePassConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $tb = new TreeBuilder();

        $rootBuilder = $tb->root('checkstyle', 'array', new NodeBuilder())
            ->attribute('label', 'Style Checks')
            ->canBeEnabled()
            ->children()
                ->booleanNode('no_trailing_whitespace')
                    ->defaultTrue()
                    ->attribute('label', 'No trailing whitespace')
                ->end()
        ;

//        $this->addLCurlyConfig($rootBuilder);
        $this->addNamingConfig($rootBuilder);

        return $tb;

    }

    private function addLCurlyConfig(NodeBuilder $rootBuilder)
    {
        $leftCurlyNode = $rootBuilder
            ->arrayNode('left_curly')
            ->addDefaultsIfNotSet()
            ->children();

        $stmts = array(
                'if' => 'same line',
                'elseif' => 'same line',
                'else' => 'same line',
                'class' => 'new line',
                'interface' => 'new line',
                'trait' => 'new line',
                'catch' => 'same line',
                'for' => 'same line',
                'foreach' => 'same line',
                'switch' => 'same line',
                'try' => 'same line',
                'while' => 'same line',
                'function' => 'new line',
                'do' => 'same line',
        );

        foreach ($stmts as $stmt => $defValue) {
            $def = new EnumNodeDefinition($stmt);
            $def->values(array('same line', 'new line', 'new line on wrap'));
            $def->defaultValue($defValue);
            $leftCurlyNode->append($def);
        }
    }

    private function addNamingConfig(NodeBuilder $rootBuilder)
    {
        $naming = array(
                'local_variable'      => '^[a-z][a-zA-Z0-9]*$',
                'abstract_class_name' => '^Abstract|Factory$',
                'utility_class_name'  => 'Utils?$',
                'constant_name'       => '^[A-Z][A-Z0-9]*(?:_[A-Z0-9]+)*$',
                'property_name'       => '^[a-z][a-zA-Z0-9]*$',
                'method_name'         => '^(?:[a-z]|__)[a-zA-Z0-9]*$',
                'parameter_name'      => '^[a-z][a-zA-Z0-9]*$',
                'interface_name'      => '^[A-Z][a-zA-Z0-9]*Interface$',
                'type_name'           => '^[A-Z][a-zA-Z0-9]*$',
                'exception_name'      => '^[A-Z][a-zA-Z0-9]*Exception$',
                'isser_method_name'   => '^(?:is|has|should|may|supports)',
        );

        $namingNode = $rootBuilder->arrayNode('naming')
            ->addDefaultsIfNotSet()
            ->canBeDisabled()
            ->children();

        foreach ($naming as $k => $def) {
            $namingNode->scalarNode($k)->defaultValue($def);
        }
    }
}