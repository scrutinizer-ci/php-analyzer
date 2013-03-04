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
use Scrutinizer\PhpAnalyzer\Model\ClassProperty;
use Scrutinizer\PhpAnalyzer\Model\ContainerPropertyInterface;
use Scrutinizer\PhpAnalyzer\Model\Property;
use Scrutinizer\PhpAnalyzer\Model\Type\PhpTypeType;

class PropertyPersister
{
    const INSERT_PROPERTY_SQL = 'INSERT INTO `properties`(`name`, `visibility`, `phpType`, `packageVersion_id`)
                                                         VALUES (?, ?, ?, ?)';
    const INSERT_CLASS_PROPERTY_SQL = 'INSERT INTO `class_properties`(`class_id`, `property_id`, `name`, `declaringClass`)
                                                                     VALUES (?, ?, ?, ?)';

    private $con;
    private $platform;
    private $phpType;

    private $propertyIdRef;

    private $propertyStmt;
    private $cPropertyStmt;

    public function __construct(Connection $con)
    {
        $this->con = $con;
        $this->platform = $con->getDatabasePlatform();
        $this->phpType = Type::getType(PhpTypeType::NAME);

        $this->propertyIdRef = new \ReflectionProperty('Scrutinizer\PhpAnalyzer\Model\Property', 'id');
        $this->propertyIdRef->setAccessible(true);

        $this->propertyStmt = $con->prepare(self::INSERT_PROPERTY_SQL);
        $this->cPropertyStmt = $con->prepare(self::INSERT_CLASS_PROPERTY_SQL);
    }

    public function persist(ContainerPropertyInterface $containerProperty, $packageVersionId, $containerId)
    {
        switch (true) {
            case $containerProperty instanceof ClassProperty:
                $stmt = $this->cPropertyStmt;
                break;

            default:
                throw new \RuntimeException('Unknown container property '.get_class($containerProperty));
        }

        $property = $containerProperty->getProperty();

        if (null === $propertyId = $property->getId()) {
            $propertyId = $this->insertProperty($property, $packageVersionId);
        }

        $stmt->bindValue(1, $containerId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $propertyId, \PDO::PARAM_INT);
        $stmt->bindValue(3, $containerProperty->getName());
        $stmt->bindValue(4, $containerProperty->isInherited() ? $containerProperty->getDeclaringClass() : null,
                                $containerProperty->isInherited() ? \PDO::PARAM_STR : \PDO::PARAM_NULL);
        $stmt->execute();
    }

    // `name`, `visibility`, `phpType`, `packageVersion_id`
    private function insertProperty(Property $property, $packageVersionId)
    {
        $this->propertyStmt->bindValue(1, $property->getName());
        $this->propertyStmt->bindValue(2, $property->getVisibility(), \PDO::PARAM_INT);
        $this->propertyStmt->bindValue(3, $this->phpType->convertToDatabaseValue($property->getPhpType(), $this->platform));
        $this->propertyStmt->bindValue(4, $packageVersionId, \PDO::PARAM_INT);

        $this->propertyStmt->execute();
        $propertyId = $this->con->lastInsertId();
        $this->propertyIdRef->setValue($property, $propertyId);

        return $propertyId;
    }
}