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

namespace Scrutinizer\PhpAnalyzer\Exception;

/**
 * Exception when the maximum allowed memory is exceeded.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class MaxMemoryExceededException extends RuntimeException
{
    private $currentUsage;
    private $maxUsage;

    public static function create($currentUsage, $maxUsage)
    {
        $ex = new self(sprintf('The maximum allowed memory usage %.3fMB has been exceeded; used %.3fMB.', $maxUsage / 1024 / 1024, $currentUsage / 1024 / 1024));
        $ex->currentUsage = $currentUsage;
        $ex->maxUsage = $maxUsage;

        return $ex;
    }

    public function __construct($msg, $code = 0, \Exception $previous = null)
    {
        parent::__construct($msg, $code, $previous);
    }

    public function getCurrentUsage()
    {
        return $this->currentUsage;
    }

    public function getMaxUsage()
    {
        return $this->maxUsage;
    }
}