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

namespace Scrutinizer\PhpAnalyzer\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition as BaseArrayNodeDefinition;

class ArrayNodeDefinition extends BaseArrayNodeDefinition
{
    public function canBeEnabled()
    {
        $this
            ->treatFalseLike(array('enabled' => false))
            ->treatNullLike(array('enabled' => true))
            ->treatTrueLike(array('enabled' => true))
            ->children()
                ->booleanNode('enabled')
                    ->defaultFalse()
                    ->attribute('label', 'Enabled')
                ->end()
            ->end()
        ;

        return $this;
    }

    public function canBeDisabled()
    {
        $this
            ->treatFalseLike(array('enabled' => false))
            ->treatNullLike(array('enabled' => true))
            ->treatTrueLike(array('enabled' => true))
            ->children()
                ->booleanNode('enabled')
                    ->defaultTrue()
                    ->attribute('label', 'Enabled')
                ->end()
            ->end()
        ;

        return $this;
    }
}