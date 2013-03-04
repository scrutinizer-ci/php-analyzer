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

use Scrutinizer\PhpAnalyzer\Analyzer;
use Scrutinizer\PhpAnalyzer\Model\File;
use Scrutinizer\PhpAnalyzer\Model\FileCollection;
use Scrutinizer\PhpAnalyzer\Util\TestUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Runs the PHP Analyzer on source code.')
            ->addArgument('dir', InputArgument::REQUIRED, 'The directory to scan.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');
        if ( ! is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }
        $dir = realpath($dir);

        if (extension_loaded('xdebug')) {
            $output->writeln('<error>It is highly recommended to disable the XDebug extension before invoking this command.</error>');
        }

        $output->write('Scanning directory... ');
        $files = FileCollection::createFromDirectory($dir);
        $output->writeln(sprintf('found <info>%d files</info>', count($files)));

        if (count($files) > 100) {
            $output->writeln('<comment>Caution: You are trying to scan a lot of files; this might be slow. For bigger libraries, consider setting up a dedicated platform or using scrutinizer-ci.com.</comment>');
        }

        $output->writeln('Starting analysis...');
        $analyzer = Analyzer::create($em = TestUtils::createTestEntityManager());
        $analyzer->setLogger(new OutputLogger($output, $input->getOption('verbose')));
        $analyzer->setRootPackageVersion($em->getRepository('PhpAnalyzer:PackageVersion')->getPackageVersion('PHP', '5.4'));
        $analyzer->analyze($files);
        $output->writeln('---------------------------------------------');
        $output->writeln('');

        foreach ($files as $file) {
            /** @var $file File */

            if ( ! $file->hasComments()) {
                continue;
            }

            $output->writeln('');
            $output->writeln($file->getName());
            $output->writeln(str_repeat('=', strlen($file->getName())));

            $comments = $file->getComments();
            ksort($comments);

            foreach ($comments as $line => $lineComments) {
                foreach ($lineComments as $comment) {
                    $output->writeln('Line '.$line.': '.$comment);
                }
            }
        }

        $output->writeln('');
        $output->writeln('Done');
    }
}