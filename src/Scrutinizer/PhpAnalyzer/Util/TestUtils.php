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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Scrutinizer\PhpAnalyzer\Model\Type\JsonArrayType;
use Scrutinizer\PhpAnalyzer\Model\Type\PhpTypeType;
use Scrutinizer\PhpAnalyzer\Model\Type\SimpleArrayType;

abstract class TestUtils
{
    public static function createTestEntityManager($temporary = false)
    {
        $options = array(
            'driver' => 'pdo_sqlite',
        );

        if ($temporary) {
            $options['memory'] = true;
        } else {
            $options['path'] = __DIR__.'/../../../../res/test_database.sqlite';
        }

        $conn = DriverManager::getConnection($options);
        $em = self::createEm($conn);

        if ($temporary) {
            $tool = new SchemaTool($em);
            $tool->createSchema($em->getMetadataFactory()->getAllMetadata());
        }

        return $em;
    }

    public static function createMysqlTestEntityManager($user, $pass, $db, $host)
    {
        $con = DriverManager::getConnection(array(
            'driver' => 'pdo_mysql',
            'user' => $user,
            'password' => $pass,
            'dbname' => $db,
            'host' => $host,
        ));
        $em = self::createEm($con);

        $tool = new SchemaTool($em);
        try {
            $tool->dropDatabase();
        } catch (\Exception $ex) {
            // tables did not exist
        }

        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        return $em;
    }

    private static function createEm(Connection $conn)
    {
        $config = new Configuration();
        $config->setProxyDir($proxyDir = sys_get_temp_dir().'/PhpAnalyzerProxies/'.uniqid('em', true));
        if (!is_dir($proxyDir) && false === @mkdir($proxyDir, 0777, true)) {
            throw new \RuntimeException(sprintf('Could not create proxy directory "%s" for test entity manager.', $proxyDir));
        }
        $config->setProxyNamespace('Scrutinizer\PhpAnalyzer\Model\Proxies');

        if (false === Type::hasType(PhpTypeType::NAME)) {
            Type::addType(PhpTypeType::NAME, 'Scrutinizer\PhpAnalyzer\Model\Type\PhpTypeType');
        }

        $driver = new AnnotationDriver(new AnnotationReader(), array(
                __DIR__.'/../Model',
        ));
        $config->setMetadataDriverImpl($driver);
        $config->addEntityNamespace('PhpAnalyzer', 'Scrutinizer\PhpAnalyzer\Model');

        $em = EntityManager::create($conn, $config);
        $em->getEventManager()->addEventListener(array('prePersist'), new TestUuidListener());

        return $em;
    }

    private final function __construct() { }
}

class TestUuidListener {
    public function prePersist($args)
    {
        if (! $args->getEntity() instanceof \Scrutinizer\PhpAnalyzer\Model\PackageVersion) {
            return;
        }

        if (null !== $args->getEntity()->getUuid()) {
            return;
        }

        $args->getEntity()->setUuid(uniqid(mt_rand(), true));
    }
}