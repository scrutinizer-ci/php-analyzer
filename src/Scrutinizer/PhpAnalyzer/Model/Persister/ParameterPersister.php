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
use Scrutinizer\PhpAnalyzer\Model\AbstractFunction;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\Method;
use Scrutinizer\PhpAnalyzer\Model\Parameter;
use Scrutinizer\PhpAnalyzer\Model\Type\PhpTypeType;

class ParameterPersister
{
    const INSERT_FUNC_SQL = 'INSERT INTO `function_parameters`(`function_id`, `name`, `phpType`, `index_nb`,
                                                               `passedByRef`, `optional`) VALUES (?, ?, ?, ?, ?, ?)';
    const INSERT_METHOD_SQL = 'INSERT INTO `method_parameters`(`method_id`, `name`, `phpType`, `index_nb`, `passedByRef`,
                                                               `optional`) VALUES (?, ?, ?, ?, ?, ?)';

    private $con;
    private $platform;
    private $phpType;
    private $fStmt;
    private $mStmt;

    public function __construct(Connection $con)
    {
        $this->con = $con;
        $this->platform = $con->getDatabasePlatform();
        $this->phpType = Type::getType(PhpTypeType::NAME);

        $this->fStmt = $con->prepare(self::INSERT_FUNC_SQL);
        $this->mStmt = $con->prepare(self::INSERT_METHOD_SQL);
    }

    public function persist(Parameter $param, AbstractFunction $function)
    {
        switch (true) {
            case $function instanceof GlobalFunction:
                $stmt = $this->fStmt;
                break;

            case $function instanceof Method:
                $stmt = $this->mStmt;
                break;

            default:
                throw new \InvalidArgumentException('Unsupported AbstractFunction implementation '.get_class($function));
        }

        $stmt->bindValue(1, $function->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(2, $param->getName());
        $stmt->bindValue(3, $this->phpType->convertToDatabaseValue($param->getPhpType(), $this->platform));
        $stmt->bindValue(4, $param->getIndex(), \PDO::PARAM_INT);
        $stmt->bindValue(5, $param->isPassedByRef() ? 1: 0, \PDO::PARAM_INT);
        $stmt->bindValue(6, $param->isOptional() ? 1 : 0, \PDO::PARAM_INT);
        $stmt->execute();
    }
}