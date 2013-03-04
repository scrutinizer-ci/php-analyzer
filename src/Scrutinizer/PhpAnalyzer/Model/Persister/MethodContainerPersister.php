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

use Doctrine\DBAL\Driver\Connection;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\InterfaceC;
use Scrutinizer\PhpAnalyzer\Model\MethodContainer;
use Scrutinizer\PhpAnalyzer\Model\TraitC;

class MethodContainerPersister
{
    const INSERT_STMT = 'INSERT INTO `method_containers`(`name`, `normalized`, `packageVersion_id`, `type`, `superClass`,
                                                         `superClasses`, `implementedInterfaces`, `modifier`,
                                                         `extendedInterfaces`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

    private $con;
    private $platform;
    private $insertStmt;
    private $methodPersister;
    private $propertyPersister;
    private $constantPersister;

    public function __construct(Connection $con, MethodPersister $methodPersister, PropertyPersister $propertyPersister, ConstantPersister $constantPersister)
    {
        $this->con = $con;
        $this->insertStmt = $con->prepare(self::INSERT_STMT);
        $this->methodPersister = $methodPersister;
        $this->propertyPersister = $propertyPersister;
        $this->constantPersister = $constantPersister;
    }

    public function persist(MethodContainer $container, $packageVersionId)
    {
        switch (true) {
            case $container instanceof Clazz:
                $type = 'class';
                $superClass = $container->getSuperClass();

                if ($superClasses = $container->getSuperClasses()) {
                    $superClasses = implode(',', $superClasses);
                } else {
                    $superClasses = null;
                }

                if ($implementedInterfaces = $container->getImplementedInterfaces()) {
                    $implementedInterfaces = implode(',', $implementedInterfaces);
                } else {
                    $implementedInterfaces = null;
                }

                $modifier = $container->getModifier();
                $extendedInterfaces = null;

                break;

            case $container instanceof InterfaceC:
                $type = 'interface';
                $superClass = null;
                $superClasses = null;
                $implementedInterfaces = null;
                $modifier = 0;

                if ($extendedInterfaces = $container->getExtendedInterfaces()) {
                    $extendedInterfaces = implode(',', $extendedInterfaces);
                } else {
                    $extendedInterfaces = null;
                }

                break;

            case $container instanceof TraitC:
                $type = 'trait';
                $superClass = null;
                $superClasses = null;
                $implementedInterfaces = null;
                $modifier = 0;
                $extendedInterfaces = null;
                break;

            default:
                throw new \InvalidArgumentException('Unknown MethodContainer '.get_class($container));
        }

        $this->insertStmt->bindValue(1, $container->getName());
        $this->insertStmt->bindValue(2, $container->isNormalized() ? 1 : 0, \PDO::PARAM_INT);
        $this->insertStmt->bindValue(3, $packageVersionId, \PDO::PARAM_INT);
        $this->insertStmt->bindValue(4, $type);
        $this->insertStmt->bindValue(5, $superClass, null === $superClass ? \PDO::PARAM_NULL : \PDO::PARAM_STR);
        $this->insertStmt->bindValue(6, $superClasses, null === $superClasses ? \PDO::PARAM_NULL : \PDO::PARAM_STR);
        $this->insertStmt->bindValue(7, $implementedInterfaces, null === $implementedInterfaces ? \PDO::PARAM_NULL : \PDO::PARAM_STR);
        $this->insertStmt->bindValue(8, $modifier, \PDO::PARAM_INT);
        $this->insertStmt->bindValue(9, $extendedInterfaces, null === $extendedInterfaces ? \PDO::PARAM_NULL : \PDO::PARAM_STR);

        $this->insertStmt->execute();
        $containerId = $this->con->lastInsertId();

        foreach ($container->getMethods() as $method) {
            $this->methodPersister->persist($method, $packageVersionId, $containerId);
        }

        switch (true) {
            case $container instanceof Clazz:
                foreach ($container->getProperties() as $property) {
                    $this->propertyPersister->persist($property, $packageVersionId, $containerId);
                }
                foreach ($container->getConstants() as $constant) {
                    $this->constantPersister->persist($constant, $packageVersionId, $containerId);
                }

                break;

            case $container instanceof InterfaceC:
                foreach ($container->getConstants() as $constant) {
                    $this->constantPersister->persist($constant, $packageVersionId, $containerId);
                }

                break;

            case $container instanceof TraitC:
                break;

            default:
                throw new \InvalidArgumentException('Unknown MethodContainer '.get_class($container));
        }
    }
}