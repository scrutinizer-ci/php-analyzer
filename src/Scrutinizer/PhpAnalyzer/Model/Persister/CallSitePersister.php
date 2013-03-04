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
use Scrutinizer\PhpAnalyzer\Model\CallGraph\CallSite;
use Scrutinizer\PhpAnalyzer\Model\CallGraph\FunctionToFunctionCallSite;
use Scrutinizer\PhpAnalyzer\Model\CallGraph\FunctionToMethodCallSite;
use Scrutinizer\PhpAnalyzer\Model\CallGraph\MethodToFunctionCallSite;
use Scrutinizer\PhpAnalyzer\Model\CallGraph\MethodToMethodCallSite;
use Scrutinizer\PhpAnalyzer\Model\Type\PhpTypeType;

class CallSitePersister
{
    const INSERT_SQL = 'INSERT INTO `call_sites`(`type`, `sourceFunction_id`, `targetFunction_id`, `targetMethod_id`,
                                                 `sourceMethod_id`) VALUES (?, ?, ?, ?, ?)';

    private $con;
    private $platform;
    private $phpType;
    private $insertStmt;
    private $argPersister;
    private $idRef;

    public function __construct(Connection $con, ArgumentPersister $argPersister)
    {
        $this->con = $con;
        $this->platform = $con->getDatabasePlatform();
        $this->phpType = Type::getType(PhpTypeType::NAME);
        $this->insertStmt = $con->prepare(self::INSERT_SQL);
        $this->argPersister = $argPersister;
        $this->idRef = new \ReflectionProperty('Scrutinizer\PhpAnalyzer\Model\CallGraph\CallSite', 'id');
        $this->idRef->setAccessible(true);
    }

    public function persist(CallSite $callSite)
    {
        // In this case, the call site has already been persisted.
        if (null !== $callSite->getId()) {
            return;
        }

        $sourceFunctionId = $targetFunctionId = $sourceMethodId = $targetMethodId = null;
        switch (true) {
            case $callSite instanceof FunctionToFunctionCallSite:
                $type = 'f2f';
                $sourceFunctionId = $sourceId = $callSite->getSource()->getId();
                $targetFunctionId = $targetId = $callSite->getTarget()->getId();
                break;

            case $callSite instanceof FunctionToMethodCallSite:
                $type = 'f2m';
                $sourceFunctionId = $sourceId = $callSite->getSource()->getId();
                $targetMethodId = $targetId = $callSite->getTarget()->getId();
                break;

            case $callSite instanceof MethodToFunctionCallSite:
                $type = 'm2f';
                $sourceMethodId = $sourceId = $callSite->getSource()->getId();
                $targetFunctionId= $targetId = $callSite->getTarget()->getId();
                break;

            case $callSite instanceof MethodToMethodCallSite:
                $type = 'm2m';
                $sourceMethodId = $sourceId = $callSite->getSource()->getId();
                $targetMethodId = $targetId = $callSite->getTarget()->getId();
                break;

            default:
                throw new \InvalidArgumentException('Unknown call site '.get_class($callSite));
        }

        // If either target, or source have not yet been persisted, we
        // simply ignore this call as there will be a second call when
        // both are complete.
        if (null === $sourceId || null === $targetId) {
            return;
        }

        $this->insertStmt->bindValue(1, $type);
        $this->insertStmt->bindValue(2, $sourceFunctionId, $sourceFunctionId ? \PDO::PARAM_INT : \PDO::PARAM_NULL);
        $this->insertStmt->bindValue(3, $targetFunctionId, $targetFunctionId ? \PDO::PARAM_INT : \PDO::PARAM_NULL);
        $this->insertStmt->bindValue(4, $targetMethodId, $targetMethodId ? \PDO::PARAM_INT : \PDO::PARAM_NULL);
        $this->insertStmt->bindValue(5, $sourceMethodId, $sourceMethodId ? \PDO::PARAM_INT : \PDO::PARAM_NULL);
        $this->insertStmt->execute();

        $callSiteId = $this->con->lastInsertId();
        $this->idRef->setValue($callSite, $callSiteId);

        if ("0" == $callSiteId) {
            throw new \LogicException(sprintf('Could not retrieve id of call-site: '.json_encode(array(
                'type' => $type,
                'source_function_id' => $sourceFunctionId,
                'target_function_id' => $targetFunctionId,
                'target_method_id' => $targetMethodId,
                'source_method_id' => $sourceMethodId,
            ))));
        }

        foreach ($callSite->getArgs() as $arg) {
            $this->argPersister->persist($arg, $callSiteId);
        }
    }
}