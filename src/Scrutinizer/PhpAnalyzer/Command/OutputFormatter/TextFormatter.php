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

namespace Scrutinizer\PhpAnalyzer\Command\OutputFormatter;

use Scrutinizer\PhpAnalyzer\Command\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Format the output of PhpAnalyzer as human readable text.
 */
class TextFormatter implements OutputFormatterInterface
{
    public function write(OutputInterface $output, $fileCollection)
    {
        $output->writeln('---------------------------------------------');
        $output->writeln('');

        foreach ($fileCollection as $file) {
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
