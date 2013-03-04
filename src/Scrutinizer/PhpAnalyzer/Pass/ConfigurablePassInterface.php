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

/**
 * Interface for passes that want to expose configurable options.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
interface ConfigurablePassInterface extends AnalysisPassInterface
{
    /**
     * Returns the available configuration options for this pass.
     *
     * Passes usually should come with a default configuration, and not have
     * any required values which first need to be entered by the user.
     *
     * @return TreeBuilder
     */
    function getConfiguration();

    /**
     * Sets the concrete values entered by the user.
     *
     * @param array $values
     */
    function setConfigurationValues(array $values);
}