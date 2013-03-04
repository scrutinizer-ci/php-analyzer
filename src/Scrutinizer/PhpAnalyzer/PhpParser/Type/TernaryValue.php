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

namespace Scrutinizer\PhpAnalyzer\PhpParser\Type;

/**
 * A pseudo-enum class used in equality tests.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
final class TernaryValue
{
    private static $instances = array();

    private $type;

    public static function forBoolean($boolean)
    {
        return self::get($boolean ? 'true' : 'false');
    }

    public static function get($type)
    {
        if ('true' !== $type && 'false' !== $type && 'unknown' !== $type) {
            throw new \InvalidArgumentException(sprintf('Invalid type "%s" passed.', $type));
        }

        if (isset(self::$instances[$type])) {
            return self::$instances[$type];
        }

        return self::$instances[$type] = new self($type);
    }

    private function __construct($type)
    {
        $this->type = $type;
    }

    public function andOp(TernaryValue $that)
    {
        switch ($this->type) {
            case 'false':
                return $this;

            case 'true':
                return $that;

            case 'unknown':
                return self::get(self::get('false') === $that ? 'false' : 'unknown');
        }

        throw new \InvalidArgumentException('switch() is exhaustive.');
    }

    public function notOp(TernaryValue $that)
    {
        switch ($this->type) {
            case 'false':
                return self::get('true');

            case 'true':
                return self::get('false');

            case 'unknown':
                return $this;
        }

        throw new \InvalidArgumentException('switch() is exhaustive.');
    }

    public function orOp(TernaryValue $that)
    {
        switch ($this->type) {
            case 'false':
                return $that;

            case 'true':
                return $this;

            case 'unknown':
                return self::get(self::get('true') === $that ? 'true' : 'unknown');
        }

        throw new \InvalidArgumentException('switch() is exhaustive.');
    }

    public function xorOp(TernaryValue $that)
    {
        switch ($this->type) {
            case 'false':
                return $that;

            case 'true':
                return $that->notOp();

            case 'unknown':
                return $this;
        }

        throw new \InvalidArgumentException('switch() is exhaustive.');
    }

    public function __toString()
    {
        return $this->type;
    }

    public function toBoolean($booleanForUnknown)
    {
        switch ($this->type) {
            case 'false':
                return false;

            case 'true':
                return true;

            case 'unknown':
                return $booleanForUnknown;
        }

        throw new \InvalidArgumentException('switch() is exhaustive.');
    }
}