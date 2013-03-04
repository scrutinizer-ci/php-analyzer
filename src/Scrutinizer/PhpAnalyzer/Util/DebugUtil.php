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

namespace Scrutinizer\PhpAnalyzer\Util;

abstract class DebugUtil
{
    public static function printBacktrace(array $trace)
    {
        foreach ($trace as $entry) {
            unset($entry['object']);

            if (isset($entry['args'])) {
                $entry['args'] = self::sanitizeArgs($entry['args']);
            }

            var_dump($entry);
        }
    }

    private static function sanitizeArgs(array $args)
    {
        foreach ($args as $k => $arg) {
            if (is_object($arg)) {
                $args[$k] = get_class($arg);
            } else if (is_array($arg)) {
                $args[$k] = self::sanitizeArgs($arg);
            }
        }

        return $args;
    }

    private final function __construct() { }
}