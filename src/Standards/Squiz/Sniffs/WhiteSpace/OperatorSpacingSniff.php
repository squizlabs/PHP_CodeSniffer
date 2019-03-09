<?php
/**
 * Verifies that operators have valid spacing surrounding them.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Sniffs\Parentheses;
use PHP_CodeSniffer\Util\Sniffs\TokenIs;
use PHP_CodeSniffer\Util\Tokens;

class OperatorSpacingSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];

    /**
     * Allow newlines instead of spaces.
     *
     * @var boolean
     */
    public $ignoreNewlines = false;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $targets   = Tokens::$comparisonTokens;
        $targets  += Tokens::$operators;
        $targets  += Tokens::$assignmentTokens;
        $targets[] = T_INLINE_THEN;
        $targets[] = T_INLINE_ELSE;

        return $targets;

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being checked.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($this->isOperator($phpcsFile, $stackPtr) === false) {
            return;
        }

        if ($tokens[$stackPtr]['code'] === T_BITWISE_AND) {
            // Check there is one space before the & operator.
            if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE) {
                $error = 'Expected 1 space before "&" operator; 0 found';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBeforeAmp');
                if ($fix === true) {
                    $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
                }

                $phpcsFile->recordMetric($stackPtr, 'Space before operator', 0);
            } else {
                if ($tokens[($stackPtr - 2)]['line'] !== $tokens[$stackPtr]['line']) {
                    $found = 'newline';
                } else {
                    $found = $tokens[($stackPtr - 1)]['length'];
                }

                $phpcsFile->recordMetric($stackPtr, 'Space before operator', $found);
                if ($found !== 1
                    && ($found !== 'newline' || $this->ignoreNewlines === false)
                ) {
                    $error = 'Expected 1 space before "&" operator; %s found';
                    $data  = [$found];
                    $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBeforeAmp', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
                    }
                }
            }//end if

            // Check there is one space after the & operator.
            if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
                $error = 'Expected 1 space after "&" operator; 0 found';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfterAmp');
                if ($fix === true) {
                    $phpcsFile->fixer->addContent($stackPtr, ' ');
                }

                $phpcsFile->recordMetric($stackPtr, 'Space after operator', 0);
            } else {
                if ($tokens[($stackPtr + 2)]['line'] !== $tokens[$stackPtr]['line']) {
                    $found = 'newline';
                } else {
                    $found = $tokens[($stackPtr + 1)]['length'];
                }

                $phpcsFile->recordMetric($stackPtr, 'Space after operator', $found);
                if ($found !== 1
                    && ($found !== 'newline' || $this->ignoreNewlines === false)
                ) {
                    $error = 'Expected 1 space after "&" operator; %s found';
                    $data  = [$found];
                    $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfterAmp', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                    }
                }
            }//end if

            return;
        }//end if

        $operator = $tokens[$stackPtr]['content'];

        if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE
            && (($tokens[($stackPtr - 1)]['code'] === T_INLINE_THEN
            && $tokens[($stackPtr)]['code'] === T_INLINE_ELSE) === false)
        ) {
            $error = "Expected 1 space before \"$operator\"; 0 found";
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBefore');
            if ($fix === true) {
                $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
            }

            $phpcsFile->recordMetric($stackPtr, 'Space before operator', 0);
        } else if (isset(Tokens::$assignmentTokens[$tokens[$stackPtr]['code']]) === false) {
            // Don't throw an error for assignments, because other standards allow
            // multiple spaces there to align multiple assignments.
            if ($tokens[($stackPtr - 2)]['line'] !== $tokens[$stackPtr]['line']) {
                $found = 'newline';
            } else {
                $found = $tokens[($stackPtr - 1)]['length'];
            }

            $phpcsFile->recordMetric($stackPtr, 'Space before operator', $found);
            if ($found !== 1
                && ($found !== 'newline' || $this->ignoreNewlines === false)
            ) {
                $error = 'Expected 1 space before "%s"; %s found';
                $data  = [
                    $operator,
                    $found,
                ];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBefore', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    if ($found === 'newline') {
                        $i = ($stackPtr - 2);
                        while ($tokens[$i]['code'] === T_WHITESPACE) {
                            $phpcsFile->fixer->replaceToken($i, '');
                            $i--;
                        }
                    }

                    $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
                    $phpcsFile->fixer->endChangeset();
                }
            }//end if
        }//end if

        if (isset($tokens[($stackPtr + 1)]) === false) {
            return;
        }

        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            // Skip short ternary such as: "$foo = $bar ?: true;".
            if (($tokens[$stackPtr]['code'] === T_INLINE_THEN
                && $tokens[($stackPtr + 1)]['code'] === T_INLINE_ELSE)
            ) {
                return;
            }

            $error = "Expected 1 space after \"$operator\"; 0 found";
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfter');
            if ($fix === true) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            }

            $phpcsFile->recordMetric($stackPtr, 'Space after operator', 0);
        } else {
            if (isset($tokens[($stackPtr + 2)]) === true
                && $tokens[($stackPtr + 2)]['line'] !== $tokens[$stackPtr]['line']
            ) {
                $found = 'newline';
            } else {
                $found = $tokens[($stackPtr + 1)]['length'];
            }

            $phpcsFile->recordMetric($stackPtr, 'Space after operator', $found);
            if ($found !== 1
                && ($found !== 'newline' || $this->ignoreNewlines === false)
            ) {
                $error = 'Expected 1 space after "%s"; %s found';
                $data  = [
                    $operator,
                    $found,
                ];

                $nextNonWhitespace = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
                if ($nextNonWhitespace !== false
                    && isset(Tokens::$commentTokens[$tokens[$nextNonWhitespace]['code']]) === true
                    && $found === 'newline'
                ) {
                    // Don't auto-fix when it's a comment or PHPCS annotation on a new line as
                    // it causes fixer conflicts and can cause the meaning of annotations to change.
                    $phpcsFile->addError($error, $stackPtr, 'SpacingAfter', $data);
                } else {
                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfter', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                    }
                }
            }//end if
        }//end if

    }//end process()


    /**
     * Checks if an operator is actually a different type of token in the current context.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being checked.
     * @param int                         $stackPtr  The position of the operator in
     *                                               the stack.
     *
     * @return boolean
     */
    protected function isOperator(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Skip default values in function declarations.
        // Skip declare statements.
        if ($tokens[$stackPtr]['code'] === T_EQUAL
            || $tokens[$stackPtr]['code'] === T_MINUS
        ) {
            if (Parentheses::lastOwnerIn($phpcsFile, $stackPtr, [T_FUNCTION, T_CLOSURE, T_DECLARE]) !== false) {
                return false;
            }
        }

        if ($tokens[$stackPtr]['code'] === T_EQUAL) {
            // Skip for '=&' case.
            if (isset($tokens[($stackPtr + 1)]) === true
                && $tokens[($stackPtr + 1)]['code'] === T_BITWISE_AND
            ) {
                return false;
            }
        }

        if ($tokens[$stackPtr]['code'] === T_BITWISE_AND) {
            // If it's not a reference, then we expect one space either side of the
            // bitwise operator.
            if (TokenIs::isReference($phpcsFile, $stackPtr) === true) {
                return false;
            }
        }

        if ($tokens[$stackPtr]['code'] === T_MINUS || $tokens[$stackPtr]['code'] === T_PLUS) {
            // Check that we aren't just assigning a minus/plus value or returning one.
            if (TokenIs::isUnaryPlusMinus($phpcsFile, $stackPtr) === true) {
                return false;
            }
        }

        return true;

    }//end isOperator()


}//end class
