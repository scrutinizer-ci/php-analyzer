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

namespace Scrutinizer\PhpAnalyzer\Model\Persister;

use Doctrine\DBAL\Connection;
use Scrutinizer\PhpAnalyzer\Model\PackageVersion;

/**
 * Specialized persister for PackageVersion instances.
 *
 * This persister is used for inserting built packages as we might have a huge number of objects, and Doctrine's default
 * UoW just takes too long to run because of the extra operations that it performs.
 *
 * These specialized persisters operate on a "insert and forget" basis not caring about change tracking etc.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class PackageVersionPersister
{
    private $con;
    private $containerPersister;
    private $functionPersister;
    private $constantPersister;

    public function __construct(Connection $con, MethodContainerPersister $containerPersister, FunctionPersister $functionPersister, GlobalConstantPersister $constantPersister)
    {
        $this->con = $con;
        $this->containerPersister = $containerPersister;
        $this->functionPersister = $functionPersister;
        $this->constantPersister = $constantPersister;
    }

    public function persist(PackageVersion $packageVersion)
    {
        if (null === $packageVersionId = $packageVersion->getId()) {
            throw new \InvalidArgumentException('The $packageVersion must already have been persisted to the database.');
        }

        // We are ensuring that all inserts are executed within a transaction.
        // See: http://dev.mysql.com/doc/refman/5.5/en/optimizing-innodb-bulk-data-loading.html
        $this->con->beginTransaction();
        try {
            foreach ($packageVersion->getContainers() as $container) {
                $this->containerPersister->persist($container, $packageVersionId);
            }
            foreach ($packageVersion->getFunctions() as $function) {
                $this->functionPersister->persist($function, $packageVersionId);
            }
            foreach ($packageVersion->getConstants() as $constant) {
                $this->constantPersister->persist($constant, $packageVersionId);
            }

            $this->con->commit();
        } catch (\Exception $ex) {
            $this->con->rollBack();

            throw $ex;
        }
    }
}