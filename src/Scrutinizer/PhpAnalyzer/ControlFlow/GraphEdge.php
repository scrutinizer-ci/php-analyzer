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

namespace Scrutinizer\PhpAnalyzer\ControlFlow;

class GraphEdge
{
    /** Edge is taken if the condition is true. */
    const TYPE_ON_TRUE = 1;
    /** Edge is taken if the condition is false. */
    const TYPE_ON_FALSE = 2;
    /** Unconditional branch. */
    const TYPE_UNCOND = 3;
    /** Exception related. */
    const TYPE_ON_EX = 4;

    private $source;
    private $type;
    private $dest;
    private $attributes = array();

    public static function getLiteral($type)
    {
        switch ($type) {
            case self::TYPE_ON_TRUE:
                return 'ON_TRUE';

            case self::TYPE_ON_FALSE:
                return 'ON_FALSE';

            case self::TYPE_UNCOND:
                return 'UNCOND';

            case self::TYPE_ON_EX:
                return 'ON_EX';

            default:
                throw new \LogicException(sprintf('Unknown type "%s".', $type));
        }
    }

    public function __construct(GraphNode $source, $type, GraphNode $dest)
    {
        $this->source = $source;
        $this->type = $type;
        $this->dest = $dest;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDest()
    {
        return $this->dest;
    }

    public function isConditional()
    {
        return self::TYPE_ON_TRUE === $this->type || self::TYPE_ON_FALSE === $this->type;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    public function getAttribute($key, $default = null)
    {
        return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : null;
    }

    public function removeAttribute($key)
    {
        unset($this->attributes[$key]);
    }
}