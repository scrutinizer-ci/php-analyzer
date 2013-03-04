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

namespace Scrutinizer\PhpAnalyzer\Model;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Scrutinizer\PhpAnalyzer\Analyzer;

/**
 * PHP Package Scanner.
 *
 * Scans packages for classes, functions, constants, etc.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class PackageScanner
{
    private $analyzer;
    private $logger;
    private $scanned = false;

    public function __construct(Analyzer $analyzer, LoggerInterface $logger = null)
    {
        $this->analyzer = $analyzer;
        $this->logger = $logger ?: new NullLogger();
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->analyzer->setLogger($logger);
    }

    public function scanZipFile(PackageVersion $rootPackageVersion, $zipFile)
    {
        $this->logger->info(sprintf('Scanning zip file "%s" for files', $zipFile));
        $files = FileCollection::createFromZipFile($zipFile, '*.php');
        $this->logger->info(sprintf('Found "%d" files.', count($files)));

        $this->scan($rootPackageVersion, $files);
    }

    public function scanDirectory(PackageVersion $rootPackageVersion, $directory)
    {
        $this->logger->info(sprintf('Scanning directory "%s" for files', $directory));
        $files = FileCollection::createFromDirectory($directory, '*.php');
        $this->logger->info(sprintf('Found "%d" files.', count($files)));

        $this->scan($rootPackageVersion, $files);
    }

    public function scan(PackageVersion $rootPackageVersion, FileCollection $col)
    {
        // This is due to the fact that we extract the scanned classes from the
        // type registry, and you basically need a new analyzer with a new set
        // of its dependencies as well. It is simply easier to create a new one
        // than to add logic which resets the internal state of all of these
        // classes.
        if ($this->scanned) {
            throw new \LogicException('scan() is a one-time operation. Please create a new PackageScanner instance.');
        }
        $this->scanned = true;

        $this->analyzer->setRootPackageVersion($rootPackageVersion);
        $this->analyzer->analyze($col);

        $registry = $this->analyzer->getTypeRegistry();
        foreach ($registry->getClasses() as $class) {
            $rootPackageVersion->addContainer($class);
        }
        foreach ($registry->getFunctions() as $function) {
            $rootPackageVersion->addFunction($function);
        }
        foreach ($registry->getConstants() as $constant) {
            $rootPackageVersion->addConstant($constant);
        }
    }
}