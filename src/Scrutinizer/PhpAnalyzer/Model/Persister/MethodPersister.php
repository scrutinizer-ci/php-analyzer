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
use Scrutinizer\PhpAnalyzer\Model\ClassMethod;
use Scrutinizer\PhpAnalyzer\Model\ContainerMethodInterface;
use Scrutinizer\PhpAnalyzer\Model\InterfaceMethod;
use Scrutinizer\PhpAnalyzer\Model\Method;
use Scrutinizer\PhpAnalyzer\Model\Type\PhpTypeType;

class MethodPersister
{
    const INSERT_METHOD_SQL = 'INSERT INTO `methods`(`returnType`, `name`, `returnByRef`, `modifier`, `visibility`,
                                                     `packageVersion_id`, `variableParameters`, `docTypes`)
                                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
    const INSERT_CLASS_METHOD_SQL = 'INSERT INTO `class_methods`(`class_id`, `method_id`, `name`, `declaringClass`)
                                        VALUES (?, ?, ?, ?)';
    const INSERT_INTERFACE_METHOD_SQL = 'INSERT INTO `interface_methods`(`interface_id`, `method_id`, `name`,
                                                                         `declaringInterface`)
                                                                        VALUES (?, ?, ?, ?)';

    private $con;
    private $platform;
    private $methodIdRef;
    private $methodStmt;
    private $cMethodStmt;
    private $iMethodStmt;
    private $phpType;
    private $paramPersister;
    private $callSitePersister;

    public function __construct(Connection $con, ParameterPersister $paramPersister, CallSitePersister $callSitePersister)
    {
        $this->con = $con;
        $this->platform = $con->getDatabasePlatform();
        $this->paramPersister = $paramPersister;
        $this->callSitePersister = $callSitePersister;

        // We rely on the fact that this type is already wired with DBAL before
        // the persister is called which should always be the case.
        $this->phpType = Type::getType(PhpTypeType::NAME);
        assert($this->phpType instanceof PhpTypeType);

        $this->methodIdRef = new \ReflectionProperty('Scrutinizer\PhpAnalyzer\Model\Method', 'id');
        $this->methodIdRef->setAccessible(true);
        $this->methodStmt = $con->prepare(self::INSERT_METHOD_SQL);
        $this->cMethodStmt = $con->prepare(self::INSERT_CLASS_METHOD_SQL);
        $this->iMethodStmt = $con->prepare(self::INSERT_INTERFACE_METHOD_SQL);
    }

    public function persist(ContainerMethodInterface $containerMethod, $packageVersionId, $containerId)
    {
        switch (true) {
            case $containerMethod instanceof ClassMethod:
                $stmt = $this->cMethodStmt;
                break;

            case $containerMethod instanceof InterfaceMethod:
                $stmt = $this->iMethodStmt;
                break;

            default:
                throw new \RuntimeException('Unknown container method '.get_class($containerMethod));
        }

        $method = $containerMethod->getMethod();

        if (null === $methodId = $method->getId()) {
            $methodId = $this->insertMethod($method, $packageVersionId);
        }

        // `class_id`, `method_id`, `name`, `declaringClass`
        $stmt->bindValue(1, $containerId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $methodId, \PDO::PARAM_INT);
        $stmt->bindValue(3, $containerMethod->getName());

        // If the method is not inherited, we only insert NULL.
        $stmt->bindValue(4, $containerMethod->isInherited() ? $containerMethod->getDeclaringClass() : null,
                                $containerMethod->isInherited() ? \PDO::PARAM_STR : \PDO::PARAM_NULL);
        $stmt->execute();
    }

    // `returnType`, `name`, `returnByRef`, `modifier`, `visibility`, `packageVersion_id`, `variableParameters`
    private function insertMethod(Method $method, $packageVersionId)
    {
        $this->methodStmt->bindValue(1, $this->phpType->convertToDatabaseValue($method->getReturnType(), $this->platform));
        $this->methodStmt->bindValue(2, $method->getName());
        $this->methodStmt->bindValue(3, $method->isReturnByRef() ? 1 : 0, \PDO::PARAM_INT);
        $this->methodStmt->bindValue(4, $method->getModifier(), \PDO::PARAM_INT);
        $this->methodStmt->bindValue(5, $method->getVisibility(), \PDO::PARAM_INT);
        $this->methodStmt->bindValue(6, $packageVersionId, \PDO::PARAM_INT);
        $this->methodStmt->bindValue(7, $method->hasVariableParameters() ? 1 : 0, \PDO::PARAM_INT);
        $this->methodStmt->bindValue(8, json_encode($method->getDocTypes()), \PDO::PARAM_STR);

        $this->methodStmt->execute();
        $this->methodIdRef->setValue($method, $methodId = $this->con->lastInsertId());

        foreach ($method->getParameters() as $param) {
            $this->paramPersister->persist($param, $method);
        }

        foreach ($method->getInCallSites() as $callSite) {
            $this->callSitePersister->persist($callSite);
        }
        foreach ($method->getOutCallSites() as $callSite) {
            $this->callSitePersister->persist($callSite);
        }

        return $methodId;
    }
}