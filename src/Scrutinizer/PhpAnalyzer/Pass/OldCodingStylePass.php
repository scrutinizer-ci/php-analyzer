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

use JMS\PhpManipulator\PhpParser\BlockNode;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\Model\File;
use Scrutinizer\PhpAnalyzer\Model\PhpFile;

class CodingStylePass extends AstAnalyzerPass
{
    private $lines;
    private $codingStyle;
    private $tokens;
    private $token;
    private $next;
    private $curLine;
    private $i;

    public function analyze(File $file)
    {
        if (!$file instanceof PhpFile) {
            return;
        }

        $this->codingStyle = $this->analyzer->getPhpCodingStyle();
        $this->tokens = token_get_all($content = $file->getContent());
        $this->i = -1;
        $this->lines = explode("\n", $content);
        $this->curLine = 1;

        parent::analyze($file);

        while ($this->next()) {
            $this->checkWhitespace();

            if (T_OPEN_TAG === $this->token[0]
                    && '<?' === $this->token[1]
                    && !$this->codingStyle->allowShortPhpOpenTag) {
                $this->addComment(1, 'Please do not use the short-opening tag, but "<?php" instead.');

                continue;
            }
        }

        // Check that file ends with a linefeed character
        if ("\n" !== substr($content, -1)) {
            $this->addComment(count($this->lines), 'Please add a linefeed (\n) at the end of the file.');
        }

        $foundTabs = $foundLinefeed = $foundTrailingWhitespace = false;
        foreach ($this->lines as $i => $line) {
            // Check that there are no tabs inside the file.
            if (!$foundTabs && preg_match('/^(?: )*\t/', $line)) {
                $this->addComment($i + 1, 'Please do not use tabs for indentation, but 4 spaces for each tab.');

                // Assume that the users fixes all further occurences of tabs.
                $foundTabs = true;

                continue;
            }

            // Check for correct line-ending character
            if (!$foundLinefeed && "\r" === substr($line, -1)) {
                $this->addComment($i + 1, 'Please do not use the line-ending (\r\n), but only \n (0x0A).');

                // Assume that the user fixed all further occurrences.
                $foundLinefeed = true;

                continue;
            }

            // Check for trailing white-space
            if (!$foundTrailingWhitespace && preg_match('/\s+$/', $line)) {
                $this->addComment($i + 1, 'Please do not add trailing whitespace.');
                $foundTrailingWhitespace = true;

                continue;
            }
        }
    }

    private function checkWhitespace()
    {
        if ('(' === $this->token[1]
                && $this->next[1][0] === ' ') {
            $this->addComment($this->curLine, 'Please do not add whitespace after the "(".');
        }
    }

    private function next()
    {
        if (null !== $this->token) {
            $this->curLine += substr_count($this->token[1], "\n");
        }

        $this->token = isset($this->tokens[$this->i + 1]) ? $this->tokens[++ $this->i] : null;
        if (is_string($this->token)) {
            $this->token = array(null, $this->token, 0);
        }

        $this->next  = null !== $this->token && isset($this->tokens[$this->i + 1]) ? $this->tokens[$this->i + 1] : null;
        if (is_string($this->next)) {
            $this->next = array(null, $this->next, 0);
        }

        return null !== $this->token;
    }

    public function shouldTraverse(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        $this->enterNode($node);

        if ($node instanceof \PHPParser_Node_Stmt_Return) {
            $this->checkBlankLineBeforeReturn($node);
        }

        return true;
    }

    private function checkBlankLineBeforeReturn(\PHPParser_Node_Stmt_Return $node)
    {
        // Check if the RETURN statement is the only statement of the parent. If
        // that is the case, just ignore the node.
        $parent = $node->getAttribute('parent');
        if ($parent instanceof BlockNode && count($parent) === 1) {
            return;
        }

        if (-1 === $line = $node->getLine()) {
            return;
        }

        // Check that there is at least one blank line before the return statement.
        // We are ignoring comments that have been made on the return statement.
        $foundBlankLine = false;
        for ($i=$line-2; $i>0; $i--) {
            $tokens = token_get_all('<?php '.$this->lines[$i]);
            array_shift($tokens); // shift of the opening tag <?php

            $hasContent = false;
            foreach ($tokens as $token) {
                // This is non-comment content, so abort the search.
                if (is_string($token)) {
                    break 2;
                }

                // If we find a comment, then this line cannot be considered empty anymore,
                // but there is still a chance that the previous line is blank.
                if (T_DOC_COMMENT === $token[0] || T_COMMENT === $token[0]) {
                    $hasContent = true;
                    break;
                }

                if (T_WHITESPACE === $token[0]) {
                    continue;
                }

                // We found something non-empty and which is also not a comment.
                break 2;
            }

            if (!$hasContent) {
                $foundBlankLine = true;
                break;
            }
        }

        if (!$foundBlankLine) {
            $this->addComment($line, 'Please add a blank line before this return statement.');
        }
    }

    /**
     * @param \PHPParser_Node $node
     */
    private function enterNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Use) {
            $this->analyzeUseStmt($node);

            return;
        }

        if ($node instanceof \PHPParser_Node_Stmt_Class) {
            $this->analyzeClass($node);

            return;
        }

        // check constant is on left side of the operand
        if ($node instanceof \PHPParser_Node_Expr_Identical
                || $node instanceof \PHPParser_Node_Expr_Equal) {
            if ($node->left instanceof \PHPParser_Node_Expr_Variable
                    && ($node->right instanceof \PHPParser_Node_Scalar
                            || $node->right instanceof \PHPParser_Node_Expr_ConstFetch)) {
                $toPrint = clone $node;
                $toPrint->left = $node->right;
                $toPrint->right = $node->left;

                $this->addComment($node->getLine(), sprintf(
                    'Please move the constant to the left side, e.g. %s. This prevents you from accidentally overwriting the variable.',
                    self::$prettyPrinter->prettyPrintExpr($toPrint)
                ));
            }
        }
    }

    private function analyzeClass(\PHPParser_Node_Stmt_Class $class)
    {
        $inProperties = $inMethods = false;
        $propertyVisibility = $methodVisibility = 3;
        $previousProperty = $previousMethod = null;

        for ($i=0,$c=count($class->stmts); $i<$c; $i++) {
            $stmt = $class->stmts[$i];

            if ($stmt instanceof \PHPParser_Node_Stmt_Property) {
                $inProperties = true;
                if ($inMethods) {
                    $this->addComment($stmt->getLine(), 'Please move properties before all method declarations in this class.');
                }

                if (count($stmt->props) > 1) {
                    $this->addComment($stmt->getLine(), 'Please use a separate statement for each property.');
                }

                if (\PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED === ($stmt->type & \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED)) {
                    $visibility = 2;
                } else if (\PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE === ($stmt->type & \PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE)) {
                    $visibility = 1;
                } else {
                    $visibility = 3;
                }

                if ($visibility > $propertyVisibility) {
                    $msg = 'Please order properties by their visibility; "public" before "protected" before "private" properties.';
                    $this->addComment($stmt->getLine(), $msg);

                    if (null !== $previousProperty) {
                        $this->addComment($previousProperty->getLine(), $msg);
                    }
                }
                $propertyVisibility = $visibility;
                $previousProperty = $stmt;
            }

            if ($stmt instanceof \PHPParser_Node_Stmt_ClassMethod) {
                $inMethods = true;

                // check if there are properties following
                $propertiesFollowing = false;
                for ($j=$i+1; $j<$c; $j++) {
                    if ($class->stmts[$j] instanceof \PHPParser_Node_Stmt_Property) {
                        $propertiesFollowing = true;
                        break;
                    }
                }

                if ($propertiesFollowing) {
                    $this->addComment($stmt->getLine(), 'Please move this method after all property declarations in this class.');
                }

                if (\PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED === ($stmt->type & \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED)) {
                    $visibility = 2;
                } else if (\PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE === ($stmt->type & \PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE)) {
                    $visibility = 1;
                } else {
                    $visibility = 3;
                }

                if ($visibility > $methodVisibility) {
                    $msg = 'Please order methods by their visibility; "public" before "protected" before "private" methods.';
                    $this->addComment($stmt->getLine(), $msg);

                    if (null !== $previousMethod) {
                        $this->addComment($previousMethod->getLine(), $msg);
                    }
                }
                $methodVisibility = $visibility;
                $previousMethod = $stmt;
            }
        }
    }

    private function analyzeUseStmt(\PHPParser_Node_Stmt_Use $useStmt)
    {
        if (count($useStmt->uses) > 1) {
            $this->addComment($useStmt->getLine(), 'Please use one "use" statement for each namespace that you import.');
        }
    }
}