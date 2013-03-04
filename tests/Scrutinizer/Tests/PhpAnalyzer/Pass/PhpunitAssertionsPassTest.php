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

namespace Scrutinizer\Tests\PhpAnalyzer\Pass;

use Scrutinizer\PhpAnalyzer\PassConfig;
use Scrutinizer\PhpAnalyzer\Pass\PhpunitAssertionsPass;

class PhpunitAssertionsPassTest extends BaseReviewingPassTest
{
    protected function getTestDir()
    {
        return __DIR__.'/Fixture/PhpunitAssertions/';
    }

    protected function getPassConfig()
    {
        $config = PassConfig::createForTypeScanning();
        $config->addPass(new PhpunitAssertionsPass());

        return $config;
    }
}