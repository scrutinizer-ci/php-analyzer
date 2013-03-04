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

namespace JMS\Tests\CodeReview\Entity\Repository;

use Scrutinizer\PhpAnalyzer\Util\TestUtils;

class MySqlEntityTypeProviderTest extends BaseEntityTypeProviderTest
{
    protected function getEntityManager()
    {
        if ( ! isset($_SERVER['MYSQL_USER'])
            || ! isset($_SERVER['MYSQL_HOST'])
            || ! isset($_SERVER['MYSQL_PASSWORD'])
            || ! isset($_SERVER['MYSQL_DATABASE'])) {
            $this->markTestSkipped('You need to configure a MySQL database, see phpunit.dist.xml');
        }

        return TestUtils::createMysqlTestEntityManager($_SERVER['MYSQL_USER'], $_SERVER['MYSQL_PASSWORD'], $_SERVER['MYSQL_DATABASE'], $_SERVER['MYSQL_HOST']);
    }
}