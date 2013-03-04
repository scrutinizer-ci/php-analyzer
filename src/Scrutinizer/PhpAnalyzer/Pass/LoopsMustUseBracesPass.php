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

namespace Scrutinizer\PhpAnalyzer\Pass;

use Scrutinizer\PhpAnalyzer\Config\NodeBuilder;
use JMS\PhpManipulator\TokenStream\PhpToken;
use Scrutinizer\PhpAnalyzer\Model\Comment;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

// TODO: This should either be removed completely, or moved to a general style check pass.
class LoopsMustUseBracesPass extends TokenStreamAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    public function getConfiguration()
    {
        $tb = new TreeBuilder();
        $tb->root('loops_must_use_braces', 'array', new NodeBuilder())
            ->attribute('label', 'Ensure all loops use braces')
            ->canBeEnabled()
        ;

        return $tb;
    }

    protected function analyzeStream()
    {
        if ( ! $this->getSetting('enabled')) {
            return;
        }

        while ($this->stream->moveNext()) {
            if ( ! $this->stream->token instanceof PhpToken) {
                continue;
            }

            switch ($this->stream->token->getType()) {
                case \T_IF:
                    $this->checkTokenWithCondition('IF');
                    break;

                case \T_ELSEIF:
                    $this->checkTokenWithCondition('ELSEIF');
                    break;

                case \T_ELSE:
                    $this->checkElse();
                    break;

                case \T_WHILE:
                    $this->checkTokenWithCondition('WHILE');
                    break;

                case \T_FOR:
                    $this->checkTokenWithCondition('FOR');
                    break;

                case \T_FOREACH:
                    $this->checkTokenWithCondition('FOREACH');
                    break;
            }
        }
    }

    private function checkElse()
    {
        if ($this->stream->next->matches(';')) {
            return;
        }

        if ($this->stream->next->matches('{')) {
            return;
        }

        // ``else if`` is allowed.
        if ($this->stream->next->matches(T_IF)) {
            return;
        }

        $this->markOffense('ELSE');
    }

    private function checkTokenWithCondition($stmtName)
    {
        $this->stream->moveNext();
        $this->stream->skipCurrentBlock();

        // No statements at all.
        if ($this->stream->next->matches(';')) {
            return;
        }

        // The user is already using braces.
        if ($this->stream->next->matches('{')) {
            return;
        }

        $this->markOffense($stmtName);
    }

    private function markOffense($stmtName)
    {
        $this->phpFile->addComment($this->stream->token->getLine(), Comment::warning(
            'coding_style.blocks_should_have_braces',
            'Please always use braces to surround the code block of %statement_name% statements.',
            array('statement_name' => $stmtName)));
    }
}