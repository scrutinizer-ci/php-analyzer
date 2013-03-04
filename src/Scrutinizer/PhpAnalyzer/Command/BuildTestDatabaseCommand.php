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

namespace Scrutinizer\PhpAnalyzer\Command;

use Doctrine\ORM\Tools\SchemaTool;
use Scrutinizer\PhpAnalyzer\Analyzer;
use Scrutinizer\PhpAnalyzer\Model\Package;
use Scrutinizer\PhpAnalyzer\Model\PackageScanner;
use Scrutinizer\PhpAnalyzer\PassConfig;
use Scrutinizer\PhpAnalyzer\Util\TestUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildTestDatabaseCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('build-test-database')
            ->setDescription('Builds the test database.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (is_file($dbFile = __DIR__.'/../../../../res/test_database.sqlite')) {
            unlink($dbFile);
        }

        $em = TestUtils::createTestEntityManager();
        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $analyzer = Analyzer::create($em, PassConfig::createForTypeScanning());
        $packageScanner = new PackageScanner($analyzer);

        $output->write('Scanning PHP 5.4 files... ');
        $package = new Package('PHP');
        $packageVersion = $package->createVersion('5.4');
        $packageVersion->setUuid(uniqid(mt_rand(), true));
        $packageScanner->scanDirectory($packageVersion, __DIR__.'/../../../../res/php-5.4-core-api');
        $output->writeln('Done');

        $output->write('Persisting PHP 5.4 files... ');
        $em->persist($package);
        $em->flush();
        $output->writeln('Done');
    }
}