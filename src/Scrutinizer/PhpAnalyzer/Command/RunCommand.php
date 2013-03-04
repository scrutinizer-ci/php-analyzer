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
use Symfony\Component\Console\Input\InputOption;
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
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'The output format ("text", "xml")', 'text')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');
        if ( ! is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }
        $dir = realpath($dir);

        $output->writeln('<comment>Caution: This command is currently only designed for small libraries; it might be slow and/or memory expensive to analyze bigger libraries.</comment>');

        $output->write('Scanning directory... ');
        $files = FileCollection::createFromDirectory($dir);
        $output->writeln(sprintf('found <info>%d files</info>', count($files)));

        $output->writeln('Starting analysis...');
        $analyzer = Analyzer::create(TestUtils::createTestEntityManager());
        $analyzer->setLogger(new OutputLogger($output, $input->getOption('verbose')));
        $analyzer->analyze($files);

        switch ($input->getOption('format')) {
            case 'xml':
                $formatter = new OutputFormatter\XmlFormatter();
                break;
            case 'text':
            default:
                $formatter = new OutputFormatter\TextFormatter();
        }
        $formatter->write($output, $files);
    }
}
