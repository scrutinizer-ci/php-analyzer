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

use Scrutinizer\PhpAnalyzer\Command\OutputFormatter;
use Symfony\Component\Console\Output\OutputInterface;

use DOMDocument;

/**
 * Format the output with XML (machine readable)
 */
class XmlFormatter implements OutputFormatter
{
    public function write(OutputInterface $output, $fileCollection)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        $root = $dom->createElement('analyzer');
        $root->setAttribute('timestamp', time());

        $dom->appendChild($root);

        foreach ($fileCollection as $file) {
            /** @var $file File */

            if ( ! $file->hasComments()) {
                continue;
            }

            $fileElement = $dom->createElement('file');
            $fileElement->setAttribute('comments', count($file->getComments()));
            $root->appendChild($fileElement);

            $comments = $file->getComments();
            ksort($comments);

            foreach ($comments as $line => $lineComments) {
                foreach ($lineComments as $comment) {
                    $commentElement = $dom->createElement('comment', $comment);
                    $commentElement->setAttribute('line', $line);

                    $fileElement->appendChild($commentElement);
                }
            }
        }

        $dom->formatOutput = true;

        $output->writeln($dom->saveXML());
    }
}

