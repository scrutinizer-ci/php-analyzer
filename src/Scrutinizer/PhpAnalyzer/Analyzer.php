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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Scrutinizer\PhpAnalyzer\Model\FileCollection;
use Scrutinizer\PhpAnalyzer\Model\PackageVersion;
use Scrutinizer\PhpAnalyzer\Model\Repository\EntityTypeProvider;
use Scrutinizer\PhpAnalyzer\Model\Type\PhpTypeType;
use Doctrine\ORM\EntityManager;
use Scrutinizer\PhpAnalyzer\Model\File;
use Scrutinizer\PhpAnalyzer\Pass\CallbackAnalysisPassInterface;
use Scrutinizer\PhpAnalyzer\Pass\ConfigurablePassInterface;
use Scrutinizer\PhpAnalyzer\Pass\RepeatedPass;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\Exception\AnalysisFailedException;
use Scrutinizer\PhpAnalyzer\Pass\TypeScanningPass;
use Scrutinizer\PhpAnalyzer\Pass\TypeInferencePass;
use Scrutinizer\PhpAnalyzer\Pass\CallGraphPass;

class Analyzer
{
    /** @var LoggerInterface */
    public $logger;

    private $passConfig;
    private $typeRegistry;
    private $baseConfig = array();
    private $pathConfigs = array();
    private $packageVersions = array();

    public static function create(EntityManager $em, PassConfig $passConfig = null)
    {
        $typeProvider = new EntityTypeProvider($em);
        $typeRegistry = new TypeRegistry($typeProvider);

        return new self($typeRegistry, $passConfig);
    }

    public function __construct(TypeRegistry $registry = null, PassConfig $passConfig = null, LoggerInterface $logger = null)
    {
        $this->passConfig = $passConfig ?: new PassConfig();
        $this->typeRegistry = $registry ?: new TypeRegistry();
        $this->logger = $logger ?: new NullLogger();
    }

    public function getPackageVersions()
    {
        return $this->packageVersions;
    }

    public function setRootPackageVersion(PackageVersion $packageVersion)
    {
        $versions = array($packageVersion);

        $addDeps = function(PackageVersion $v) use (&$versions, &$addDeps) {
            foreach ($v->getDependencies() as $dep) {
                if (in_array($dep, $versions, true)) {
                    continue;
                }

                $versions[] = $dep;
                $addDeps($dep);
            }
        };
        $addDeps($packageVersion);

        $this->setPackageVersions($versions);
    }

    public function setPackageVersions(array $packageVersions)
    {
        $this->packageVersions = $packageVersions;
        $this->getTypeRegistry()->setPackageVersions($packageVersions);
    }

    /**
     * The arrays for baseConfig, and pathConfigs are already expected to be
     * run through the merging process.
     *
     * This method will not merge the base config into the path configs anymore.
     * However, we will once more resolve these configs using the current pass
     * config. This is necessary as the passed configs until now were only run
     * through the ConfigProcessor which does not add default values for all
     * passes. The additional pass through the PassConfig ensures that each config
     * is processed individually.
     *
     * The base config is required here because if there is no path config that
     * matches a given path, then the base config is chosen.
     *
     * @param array $baseConfig
     * @param array $pathConfigs if none are given, the base config is always used
     */
    public function setConfigurationValues(array $baseConfig, array $pathConfigs = array())
    {
        $this->baseConfig = $this->passConfig->resolveValues($baseConfig);

        $this->pathConfigs = array();
        foreach ($pathConfigs as $k => $values) {
            $paths = $values['paths'];
            unset($values['paths']);

            $this->pathConfigs[$k] = $this->passConfig->resolveValues($values);
            $this->pathConfigs[$k]['paths'] = $paths;
        }
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Returns the active type registry.
     *
     * @return TypeRegistry
     */
    public function getTypeRegistry()
    {
        return $this->typeRegistry;
    }

    /**
     * @return PassConfig
     */
    public function getPassConfig()
    {
        return $this->passConfig;
    }

    public function analyze(FileCollection $files)
    {
        PhpTypeType::setTypeRegistry($this->typeRegistry);

        // If no configuration is given, we run with the default config.
        if ( ! $this->baseConfig) {
            $this->setConfigurationValues(array());
        }

        foreach ($this->passConfig->getPasses() as $pass) {
            $this->logger->debug(sprintf('Running Pass "%s"', get_class($pass)));
            $startTime = time();
            $configKey = null;

            if ($pass instanceof ConfigurablePassInterface) {
                $configKey = $pass->getConfiguration()->buildTree()->getName();
            }

            if ($pass instanceof AnalyzerAwareInterface) {
                $pass->setAnalyzer($this);
            }

            // The repeated pass has it's own processing logic which is completely
            // independent of the analyzer.
            if ($pass instanceof RepeatedPass) {
                $pass->analyze($files);

                continue;
            }

            if ($pass instanceof CallbackAnalysisPassInterface) {
                $pass->beforeAnalysis();
            }

            foreach ($files as $file) {
                /** @var $file File */

                if ($pass instanceof ConfigurablePassInterface) {
                    $hasPathConfig = false;
                    foreach ($this->pathConfigs as $pathConfig) {
                        foreach ($pathConfig['paths'] as $path) {
                            if (fnmatch($path, $file->getName())) {
                                $pass->setConfigurationValues($pathConfig[$configKey]);
                                $hasPathConfig = true;

                                break 2;
                            }
                        }
                    }

                    if ( ! $hasPathConfig) {
                        $pass->setConfigurationValues($this->baseConfig[$configKey]);
                    }
                }

                $this->logger->debug(sprintf('Analyzing File "%s"', $file->getName()));
                try {
                    $pass->analyze($file);
                } catch (\Exception $ex) {
                    // There are a few passes where we need to stop when an exception occurs. These are passes which
                    // are so essential for the following passes that we cannot run any of them if those basic passes
                    // did not complete successfully. For all other passes, we will simply log the error, and continue
                    // execution.
                    if ($pass instanceof TypeScanningPass || $pass instanceof TypeInferencePass || $pass instanceof CallGraphPass) {
                        throw AnalysisFailedException::fromException($ex, $file);
                    }

                    $this->logger->error('An error occurred during review of "'.$file->getName().'": '.$ex->getMessage(), array('exception' => $ex));
                }
            }

            if ($pass instanceof CallbackAnalysisPassInterface) {
                $pass->afterAnalysis();
            }

            $this->logger->debug(sprintf('"%s" finished after %.2f minutes.', get_class($pass), (time() - $startTime)/60));
        }
    }
}