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
use Doctrine\DBAL\Types\Type;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\Type\PhpTypeType;

class FunctionPersister
{
    const INSERT_SQL = 'INSERT INTO `global_functions`(`returnType`, `name`, `returnByRef`, `packageVersion_id`,
                                                       `variableParameters`) VALUES (?, ?, ?, ?, ?)';

    private $con;
    private $platform;
    private $phpType;
    private $paramPersister;
    private $callSitePersister;
    private $insertStmt;
    private $functionIdRef;

    public function __construct(Connection $con, ParameterPersister $paramPersister, CallSitePersister $callSitePersister)
    {
        $this->con = $con;
        $this->platform = $con->getDatabasePlatform();
        $this->phpType = Type::getType(PhpTypeType::NAME);
        $this->paramPersister = $paramPersister;
        $this->callSitePersister = $callSitePersister;
        $this->insertStmt = $con->prepare(self::INSERT_SQL);
        $this->functionIdRef = new \ReflectionProperty('Scrutinizer\PhpAnalyzer\Model\GlobalFunction', 'id');
        $this->functionIdRef->setAccessible(true);
    }

    public function persist(GlobalFunction $function, $packageVersionId)
    {
        $this->insertStmt->bindValue(1, $this->phpType->convertToDatabaseValue($function->getReturnType(), $this->platform));
        $this->insertStmt->bindValue(2, $function->getName());
        $this->insertStmt->bindValue(3, $function->isReturnByRef() ? 1 : 0, \PDO::PARAM_INT);
        $this->insertStmt->bindValue(4, $packageVersionId, \PDO::PARAM_INT);
        $this->insertStmt->bindValue(5, $function->hasVariableParameters() ? 1 : 0, \PDO::PARAM_INT);
        $this->insertStmt->execute();

        $functionId = $this->con->lastInsertId();
        $this->functionIdRef->setValue($function, $functionId);

        foreach ($function->getParameters() as $param) {
            $this->paramPersister->persist($param, $function);
        }

        foreach ($function->getInCallSites() as $callSite) {
            $this->callSitePersister->persist($callSite);
        }
        foreach ($function->getOutCallSites() as $callSite) {
            $this->callSitePersister->persist($callSite);
        }
    }
}