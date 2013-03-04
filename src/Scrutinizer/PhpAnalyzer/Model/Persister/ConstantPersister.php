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
use Scrutinizer\PhpAnalyzer\Model\ClassConstant;
use Scrutinizer\PhpAnalyzer\Model\Constant;
use Scrutinizer\PhpAnalyzer\Model\ContainerConstantInterface;
use Scrutinizer\PhpAnalyzer\Model\InterfaceConstant;
use Scrutinizer\PhpAnalyzer\Model\Type\PhpTypeType;

class ConstantPersister
{
    const INSERT_CONSTANT_SQL = 'INSERT INTO `constants`(`name`, `phpType`, `packageVersion_id`) VALUES (?, ?, ?)';
    const INSERT_CLASS_CONSTANT_SQL = 'INSERT INTO `class_constants`(`class_id`, `constant_id`, `name`, `declaringClass`)
                                                                    VALUES (?, ?, ?, ?)';
    const INSERT_INTERFACE_CONSTANT_SQL = 'INSERT INTO `interface_constants`(`interface_id`, `constant_id`, `name`, `declaringInterface`) VALUES (?, ?, ?, ?)';

    private $con;
    private $platform;
    private $phpType;

    private $constantIdRef;

    private $constantStmt;
    private $cConstantStmt;
    private $iConstantStmt;

    public function __construct(Connection $con)
    {
        $this->con = $con;
        $this->platform = $con->getDatabasePlatform();
        $this->phpType = Type::getType(PhpTypeType::NAME);

        $this->constantIdRef = new \ReflectionProperty('Scrutinizer\PhpAnalyzer\Model\Constant', 'id');
        $this->constantIdRef->setAccessible(true);

        $this->constantStmt = $con->prepare(self::INSERT_CONSTANT_SQL);
        $this->cConstantStmt = $con->prepare(self::INSERT_CLASS_CONSTANT_SQL);
        $this->iConstantStmt = $con->prepare(self::INSERT_INTERFACE_CONSTANT_SQL);
    }

    public function persist(ContainerConstantInterface $containerConstant, $packageVersionId, $containerId)
    {
        switch (true) {
            case $containerConstant instanceof ClassConstant:
                $stmt = $this->cConstantStmt;
                break;

            case $containerConstant instanceof InterfaceConstant:
                $stmt = $this->iConstantStmt;
                break;

            default:
                throw new \InvalidArgumentException(sprintf('Unsupported container constant '.get_class($containerConstant)));
        }

        $constant = $containerConstant->getConstant();
        if (null === $constantId = $constant->getId()) {
            $constantId = $this->insertConstant($constant, $packageVersionId);
        }

        $stmt->bindValue(1, $containerId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $constantId, \PDO::PARAM_INT);
        $stmt->bindValue(3, $containerConstant->getName());
        $stmt->bindValue(4, $containerConstant->isInherited() ? $containerConstant->getDeclaringClass() : null,
                                $containerConstant->isInherited() ? \PDO::PARAM_STR : \PDO::PARAM_NULL);
        $stmt->execute();
    }

    // `name`, `phpType`, `packageVersion_id`
    private function insertConstant(Constant $constant, $packageVersionId)
    {
        $this->constantStmt->bindValue(1, $constant->getName());
        $this->constantStmt->bindValue(2, $this->phpType->convertToDatabaseValue($constant->getPhpType(), $this->platform));
        $this->constantStmt->bindValue(3, $packageVersionId);

        $this->constantStmt->execute();
        $constantId = $this->con->lastInsertId();
        $this->constantIdRef->setValue($constant, $constantId);

        return $constantId;
    }
}