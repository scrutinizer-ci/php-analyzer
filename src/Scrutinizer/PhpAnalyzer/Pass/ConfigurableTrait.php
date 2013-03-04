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

use Symfony\Component\Config\Definition\ConfigurationInterface;

trait ConfigurableTrait
{
    private $cfgValues;

    public function setConfigurationValues(array $values)
    {
        $this->cfgValues = $values;

        $this->afterConfigSet();
    }

    public function getConfiguration()
    {
        if ( ! class_exists($configClass = get_class($this).'Configuration')) {
            throw new \RuntimeException(sprintf('The configuration class "%s" does not exist.', $configClass));
        }

        $configuration = new $configClass();
        if ( ! $configuration instanceof ConfigurationInterface) {
            throw new \RuntimeException(sprintf('"%s" must implement "Symfony\Component\Config\Definition\ConfigurationInterface".', $configClass));
        }

        return $configuration->getConfigTreeBuilder();
    }

    private function afterConfigSet()
    {
    }

    private function getSettingOrElse($path, $default = null)
    {
        $value = $this->cfgValues;
        foreach (explode(".", $path) as $pathElem) {
            if ( ! is_array($value)) {
                return $default;
            }

            if ( ! array_key_exists($pathElem, $value)) {
                return $default;
            }

            $value = $value[$pathElem];
        }

        return $value;
    }

    private function getSetting($path)
    {
        $value = $this->cfgValues;
        foreach (explode(".", $path) as $pathElem) {
            if ( ! is_array($value)) {
                throw new \InvalidArgumentException(sprintf('The key "%s" does not exist for path "%s" as the value is not an array.', $pathElem, $path));
            }

            if ( ! array_key_exists($pathElem, $value)) {
                throw new \InvalidArgumentException(sprintf('The key "%s" does not exist for path "%s".', $pathElem, $path));
            }

            $value = $value[$pathElem];
        }

        return $value;
    }
}