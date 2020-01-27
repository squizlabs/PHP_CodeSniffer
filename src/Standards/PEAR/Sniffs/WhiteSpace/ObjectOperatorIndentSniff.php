<?php
/**
 * Checks that object operators are indented correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ObjectOperatorIndentSniff implements Sniff
{

    /**
     * The number of spaces code should be indented.
     *
     * @var integer
     */
    public $indent = 4;

    /**
     * Indicates whether multilevel indenting is allowed.
     *
     * @var boolean
     */
    public $multilevel = false;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_OBJECT_OPERATOR];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile All the tokens found in the document.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Make sure this is the first object operator in a chain of them.
        $start = $phpcsFile->findStartOfStatement($stackPtr);
        $prev  = $phpcsFile->findPrevious(T_OBJECT_OPERATOR, ($stackPtr - 1), $start);
        if ($prev !== false) {
            return;
        }

        // Make sure this is a chained call.
        $end  = $phpcsFile->findEndOfStatement($stackPtr);
        $next = $phpcsFile->findNext(T_OBJECT_OPERATOR, ($stackPtr + 1), $end);
        if ($next === false) {
            // Not a chained call.
            return;
        }

        // Determine correct indent.
        for ($i = ($start - 1); $i >= 0; $i--) {
            if ($tokens[$i]['line'] !== $tokens[$start]['line']) {
                $i++;
                break;
            }
        }

        $baseIndent = 0;
        if ($i >= 0 && $tokens[$i]['code'] === T_WHITESPACE) {
            $baseIndent = $tokens[$i]['length'];
        }

        $baseIndent += $this->indent;

        // Determine the scope of the original object operator.
        $origBrackets = null;
        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            $origBrackets = $tokens[$stackPtr]['nested_parenthesis'];
        }

        $origConditions = null;
        if (isset($tokens[$stackPtr]['conditions']) === true) {
            $origConditions = $tokens[$stackPtr]['conditions'];
        }

        // Check indentation of each object operator in the chain.
        // If the first object operator is on a different line than
        // the variable, make sure we check its indentation too.
        if ($tokens[$stackPtr]['line'] > $tokens[$start]['line']) {
            $next = $stackPtr;
        }

        $previousIndent = $baseIndent;

        while ($next !== false) {
            // Make sure it is in the same scope, otherwise don't check indent.
            $brackets = null;
            if (isset($tokens[$next]['nested_parenthesis']) === true) {
                $brackets = $tokens[$next]['nested_parenthesis'];
            }

            $conditions = null;
            if (isset($tokens[$next]['conditions']) === true) {
                $conditions = $tokens[$next]['conditions'];
            }

            if ($origBrackets === $brackets && $origConditions === $conditions) {
                // Make sure it starts a line, otherwise don't check indent.
                $prev   = $phpcsFile->findPrevious(T_WHITESPACE, ($next - 1), $stackPtr, true);
                $indent = $tokens[($next - 1)];
                if ($tokens[$prev]['line'] !== $tokens[$next]['line']
                    && $indent['code'] === T_WHITESPACE
                ) {
                    if ($indent['line'] === $tokens[$next]['line']) {
                        $foundIndent = strlen($indent['content']);
                    } else {
                        $foundIndent = 0;
                    }

                    $minIndent      = $previousIndent;
                    $maxIndent      = $previousIndent;
                    $expectedIndent = $previousIndent;

                    if ($this->multilevel === true) {
                        $minIndent      = max(($previousIndent - $this->indent), $baseIndent);
                        $maxIndent      = ($previousIndent + $this->indent);
                        $expectedIndent = min(max($foundIndent, $minIndent), $maxIndent);
                    }

                    if ($foundIndent < $minIndent || $foundIndent > $maxIndent) {
                        $error = 'Object operator not indented correctly; expected %s spaces but found %s';
                        $data  = [
                            $expectedIndent,
                            $foundIndent,
                        ];

                        $fix = $phpcsFile->addFixableError($error, $next, 'Incorrect', $data);
                        if ($fix === true) {
                            $spaces = str_repeat(' ', $expectedIndent);
                            if ($foundIndent === 0) {
                                $phpcsFile->fixer->addContentBefore($next, $spaces);
                            } else {
                                $phpcsFile->fixer->replaceToken(($next - 1), $spaces);
                            }
                        }
                    }

                    $previousIndent = $expectedIndent;
                }//end if

                // It cant be the last thing on the line either.
                $content = $phpcsFile->findNext(T_WHITESPACE, ($next + 1), null, true);
                if ($tokens[$content]['line'] !== $tokens[$next]['line']) {
                    $error = 'Object operator must be at the start of the line, not the end';
                    $fix   = $phpcsFile->addFixableError($error, $next, 'StartOfLine');
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($x = ($next + 1); $x < $content; $x++) {
                            $phpcsFile->fixer->replaceToken($x, '');
                        }

                        $phpcsFile->fixer->addNewlineBefore($next);
                        $phpcsFile->fixer->endChangeset();
                    }
                }
            }//end if

            $next = $phpcsFile->findNext(
                T_OBJECT_OPERATOR,
                ($next + 1),
                null,
                false,
                null,
                true
            );
        }//end while

    }//end process()


}//end class
