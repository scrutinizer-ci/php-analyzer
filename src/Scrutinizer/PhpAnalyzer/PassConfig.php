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

namespace Scrutinizer\PhpAnalyzer;

use Scrutinizer\PhpAnalyzer\Pass\AnalysisPassInterface;
use Scrutinizer\PhpAnalyzer\Pass\CallGraphPass;
use Scrutinizer\PhpAnalyzer\Pass\CheckAccessControlPass;
use Scrutinizer\PhpAnalyzer\Pass\CheckForTyposPass;
use Scrutinizer\PhpAnalyzer\Pass\CheckParamExpectsRefPass;
use Scrutinizer\PhpAnalyzer\Pass\CheckUnreachableCodePass;
use Scrutinizer\PhpAnalyzer\Pass\CheckUsageContextPass;
use Scrutinizer\PhpAnalyzer\Pass\CheckVariablesPass;
use Scrutinizer\PhpAnalyzer\Pass\CheckstylePass;
use Scrutinizer\PhpAnalyzer\Pass\ConfigurablePassInterface;
use Scrutinizer\PhpAnalyzer\Pass\DeadAssignmentsDetectionPass;
use Scrutinizer\PhpAnalyzer\Pass\DocCommentFixingPass;
use Scrutinizer\PhpAnalyzer\Pass\InferTypesFromDocCommentsPass;
use Scrutinizer\PhpAnalyzer\Pass\LoggingPass;
use Scrutinizer\PhpAnalyzer\Pass\LoopsMustUseBracesPass;
use Scrutinizer\PhpAnalyzer\Pass\MarkPassedByRefArgsPass;
use Scrutinizer\PhpAnalyzer\Pass\PhpunitAssertionsPass;
use Scrutinizer\PhpAnalyzer\Pass\Psr\Psr0ComplianceCheckPass;
use Scrutinizer\PhpAnalyzer\Pass\ReflectionFixerPass;
use Scrutinizer\PhpAnalyzer\Pass\ReflectionUsageCheckPass;
use Scrutinizer\PhpAnalyzer\Pass\RepeatedPass;
use Scrutinizer\PhpAnalyzer\Pass\ReturnTypeScanningPass;
use Scrutinizer\PhpAnalyzer\Pass\SimplifyBooleanReturnPass;
use Scrutinizer\PhpAnalyzer\Pass\SuspiciousCodePass;
use Scrutinizer\PhpAnalyzer\Pass\TypeInferencePass;
use Scrutinizer\PhpAnalyzer\Pass\TypeScanningPass;
use Scrutinizer\PhpAnalyzer\Pass\UseStatementFixerPass;
use Scrutinizer\PhpAnalyzer\Pass\VariableReachabilityPass;
use Scrutinizer\PhpAnalyzer\Pass\VerifyPhpDocCommentsPass;
use Symfony\Component\Config\Definition\Processor;
use Scrutinizer\PhpAnalyzer\Pass\PrecedenceChecksPass;
use Scrutinizer\PhpAnalyzer\Pass\CheckBasicSemanticsPass;

class PassConfig
{
    const TYPE_INITIALIZING = 'initializing';
    const TYPE_ANALYZING    = 'analyzing';
    const TYPE_REVIEWING    = 'reviewing';
    const TYPE_FIXING       = 'fixing';

    private $initializingPasses = array();
    private $analyzingPasses    = array();
    private $reviewingPasses    = array();
    private $fixingPasses       = array();

    public static function createWithPasses(array $passes)
    {
        if (!$passes) {
            throw new \RuntimeException('At least one pass must be given.');
        }

        $config = new self;

        $config->initializingPasses =
        $config->analyzingPasses =
        $config->reviewingPasses = array();

        $config->fixingPasses = $passes;

        return $config;
    }

    public static function createForFixing()
    {
        $config = new self;
        $config->reviewingPasses = array();

        return $config;
    }

    public static function create()
    {
        return new self();
    }

    public static function createForTypeScanning()
    {
        $config = new self;

        $config->analyzingPasses =
        $config->reviewingPasses =
        $config->fixingPasses = array();

        return $config;
    }

    public function __construct()
    {
        $this->initializingPasses = array(
            new TypeScanningPass(),

            new LoggingPass('Running Type Inference Engine'),
            new RepeatedPass(array(
                new TypeInferencePass(),
                new MarkPassedByRefArgsPass(),
                new VariableReachabilityPass(),
                new RepeatedPass(array(
                    new TypeInferencePass(),
                    new ReturnTypeScanningPass(),
                ), 2),
                new InferTypesFromDocCommentsPass(),
            ), 2),
            new TypeInferencePass(),

            new LoggingPass('Building Call Graph'),
            new CallGraphPass(),
        );

        $this->analyzingPasses = array(
            // This pass needs to be run before CheckVariablesPass as it marks arguments
            // which might be passed by reference.
            new CheckParamExpectsRefPass(),
        );

        $this->reviewingPasses = array(
            new LoggingPass('Running Checks'),
            new CheckstylePass(),
            new CheckUnreachableCodePass(),
            new CheckAccessControlPass(),
            new CheckForTyposPass(),
            new CheckVariablesPass(),
            new SuspiciousCodePass(),
            new DeadAssignmentsDetectionPass(),
            new VerifyPhpDocCommentsPass(),
            new LoopsMustUseBracesPass(),
            new CheckUsageContextPass(),
            new CheckParamExpectsRefPass(),
            new SimplifyBooleanReturnPass(),
            new PhpunitAssertionsPass(),
            new ReflectionUsageCheckPass(),
            new PrecedenceChecksPass(),
            new CheckBasicSemanticsPass(),
        );

        $this->fixingPasses = array(
            new LoggingPass('Running Fixes'),
            new DocCommentFixingPass(),
            new ReflectionFixerPass(),
            new UseStatementFixerPass(),
        );
    }

    public function getConfigurablePasses()
    {
        $passes = array();
        foreach ($this->getPasses() as $pass) {
            if ($pass instanceof ConfigurablePassInterface) {
                $passes[] = $pass;
            }
        }

        return $passes;
    }

    public function getConfigurations()
    {
        $configs = array();

        foreach ($this->getPasses() as $pass) {
            if (!$pass instanceof ConfigurablePassInterface) {
                continue;
            }

            $configs[get_class($pass)] = $pass->getConfiguration();
        }

        return $configs;
    }

    /**
     * Processes user-entered configuration values.
     *
     * @param array $values
     */
    public function processConfigurationValues(array $values = array())
    {
        $resolvedValues = $this->resolveValues($values);

        foreach ($this->getPasses() as $pass) {
            if ( ! $pass instanceof ConfigurablePassInterface) {
                continue;
            }

            $node = $pass->getConfiguration()->buildTree();
            if (!isset($resolvedValues[$node->getName()])) {
                continue;
            }

            $pass->setConfigurationValues($resolvedValues[$node->getName()]);
        }
    }

    public function resolveValues(array $values = array())
    {
        $processor = new Processor();
        $resolvedValues = array();
        foreach ($this->getPasses() as $pass) {
            if (!$pass instanceof ConfigurablePassInterface) {
                continue;
            }

            $node = $pass->getConfiguration()->buildTree();
            $passValues = isset($values[$node->getName()]) ? $values[$node->getName()] : array();

            $resolvedValues[$node->getName()] = $processor->process($node, array($passValues));
        }

        return $resolvedValues;
    }

    public function disable($passClass)
    {
        if (!class_exists($passClass)) {
            $passClass = 'Scrutinizer\PhpAnalyzer\Pass\\'.$passClass;
        }

        $found = false;
        foreach (array('initializing', 'analyzing', 'reviewing') as $type) {
            foreach ($this->{$type.'Passes'} as $k => $pass) {
                if ($pass instanceof $passClass) {
                    unset($this->{$type.'Passes'}[$k]);
                    $found = true;
                }
            }
        }

        if (!$found) {
            throw new \RuntimeException(sprintf('Could not disable pass "%s" as it was not found.', $passClass));
        }
    }

    public function clear(array $types = null)
    {
        if (!$types) {
            $this->initializingPasses =
            $this->analyzingPasses =
            $this->fixingPasses =
            $this->reviewingPasses = array();
        } else {
            foreach ($types as $type) {
                $this->{$type.'Passes'} = array();
            }
        }
    }

    public function addPass(AnalysisPassInterface $pass, $type = self::TYPE_ANALYZING)
    {
        $this->{$type.'Passes'}[] = $pass;
    }

    public function getPasses()
    {
        return array_merge($this->initializingPasses,
                           $this->analyzingPasses,
                           $this->reviewingPasses,
                           $this->fixingPasses);
    }
}