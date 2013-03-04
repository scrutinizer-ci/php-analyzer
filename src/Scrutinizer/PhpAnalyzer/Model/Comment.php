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

namespace Scrutinizer\PhpAnalyzer\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * Holds information about an error, or warning that one of the analyzer passes
 * has found.
 *
 * @Serializer\ExclusionPolicy("ALL")
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class Comment
{
    /**
     * Comments of this type should be used for diagnostics which likely result
     * in a fatal program error at run-time.
     */
    const LEVEL_ERROR   = 'error';

    /**
     * Comments of this type should be used for diagnostics which will not cause
     * the program to abort.
     */
    const LEVEL_WARNING = 'warning';

    /** @Serializer\SerializedName("id") @Serializer\Expose */
    private $key;

    private $level;

    /** @Serializer\SerializedName("message") @Serializer\Expose */
    private $messageFormat;

    /** @Serializer\SerializedName("params") @Serializer\Expose */
    private $context;

    /** @Serializer\Expose */
    private $tool = 'php_analyzer';

    /**
     * Determines in which part of the context should be considered when making
     * equality decisions. By default, the entire context will be considered.
     *
     * @var array|null
     */
    private $variesIn;

    private $otherOccurrences = array();

    public static function error($key, $messageFormat, array $context = array())
    {
        return new self(self::LEVEL_ERROR, $key, $messageFormat, $context);
    }

    public static function warning($key, $messageFormat, array $context = array())
    {
        return new self(self::LEVEL_WARNING, $key, $messageFormat, $context);
    }

    private function __construct($level, $key, $messageFormat, array $context)
    {
        $this->level = $level;
        $this->key = $key;
        $this->messageFormat = $messageFormat;
        $this->context = $context;
    }

    public function equals(Comment $that)
    {
        if ($this->key !== $that->key) {
            return false;
        }

        if (null === $this->variesIn) {
            return $this->context === $that->context;
        }

        foreach ($this->variesIn as $var) {
            if ( ! isset($that->context[$var])) {
                return false;
            }

            if ($this->context[$var] !== $that->context[$var]) {
                return false;
            }
        }

        return true;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getMessageFormat()
    {
        return $this->messageFormat;
    }

    public function getOtherOccurrences()
    {
        return $this->otherOccurrences;
    }

    public function setOtherOccurrences(array $lines)
    {
        $this->otherOccurrences = array_unique($lines);
    }

    public function varyIn(array $vars)
    {
        foreach ($vars as $var) {
            if ( ! isset($this->context[$var])) {
                throw new \InvalidArgumentException(sprintf('The var "%s" does not exist.', $var));
            }
        }

        $this->variesIn = $vars;

        return $this;
    }

    /**
     * Returns the context of this comment.
     *
     * The context may contain additional data which describes the problem.
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    public function setContext(array $context)
    {
        $this->context = $context;
    }

    public function __toString()
    {
        $replaceMap = array();
        foreach ($this->context as $key => $value) {
            $replaceMap['%'.$key.'%'] = $value;
        }

        $msg = strtr($this->messageFormat, $replaceMap);

        switch (count($this->otherOccurrences)) {
            case 0:
                break;

            case 1:
                $msg .= ' Please also check line '.$this->otherOccurrences[0].'.';
                break;

            case 2:
            case 3:
                $occ = $this->otherOccurrences;
                $last = array_pop($occ);
                $msg .= ' I also found this on lines '.implode(', ', $occ).', and '.$last.'.';
                break;

            default:
                $msg .= ' I found this a couple of more times on lines: '.implode(", ", $this->otherOccurrences).'.';
                break;
        }

        return $msg;
    }
}