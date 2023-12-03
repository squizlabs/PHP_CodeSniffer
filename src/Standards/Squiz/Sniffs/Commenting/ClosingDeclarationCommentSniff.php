<?php
/**
 * Checks the //end ... comments on classes, interfaces and functions.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ClosingDeclarationCommentSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_FUNCTION,
            T_CLASS,
            T_INTERFACE,
            T_ENUM,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens..
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_FUNCTION) {
            $methodProps = $phpcsFile->getMethodProperties($stackPtr);

            // Abstract methods do not require a closing comment.
            if ($methodProps['is_abstract'] === true) {
                return;
            }

            // If this function is in an interface then we don't require
            // a closing comment.
            if ($phpcsFile->hasCondition($stackPtr, T_INTERFACE) === true) {
                return;
            }

            if (isset($tokens[$stackPtr]['scope_closer']) === false) {
                $error = 'Possible parse error: non-abstract method defined as abstract';
                $phpcsFile->addWarning($error, $stackPtr, 'Abstract');
                return;
            }

            $decName = $phpcsFile->getDeclarationName($stackPtr);
            $comment = '//end '.$decName.'()';
        } else if ($tokens[$stackPtr]['code'] === T_CLASS) {
            $comment = '//end class';
        } else if ($tokens[$stackPtr]['code'] === T_INTERFACE) {
            $comment = '//end interface';
        } else {
            $comment = '//end enum';
        }//end if

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            $error = 'Possible parse error: %s missing opening or closing brace';
            $data  = [$tokens[$stackPtr]['content']];
            $phpcsFile->addWarning($error, $stackPtr, 'MissingBrace', $data);
            return;
        }

        $closingBracket = $tokens[$stackPtr]['scope_closer'];

        if ($closingBracket === null) {
            // Possible inline structure. Other tests will handle it.
            return;
        }

        $data = [$comment];
        if (isset($tokens[($closingBracket + 1)]) === false || $tokens[($closingBracket + 1)]['code'] !== T_COMMENT) {
            $next = $phpcsFile->findNext(T_WHITESPACE, ($closingBracket + 1), null, true);
            if (rtrim($tokens[$next]['content']) === $comment) {
                // The comment isn't really missing; it is just in the wrong place.
                $fix = $phpcsFile->addFixableError('Expected %s directly after closing brace', $closingBracket, 'Misplaced', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($closingBracket + 1); $i < $next; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    // Just in case, because indentation fixes can add indents onto
                    // these comments and cause us to be unable to fix them.
                    $phpcsFile->fixer->replaceToken($next, $comment.$phpcsFile->eolChar);
                    $phpcsFile->fixer->endChangeset();
                }
            } else {
                $fix = $phpcsFile->addFixableError('Expected %s', $closingBracket, 'Missing', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($closingBracket, '}'.$comment.$phpcsFile->eolChar);
                }
            }

            return;
        }//end if

        if (rtrim($tokens[($closingBracket + 1)]['content']) !== $comment) {
            $fix = $phpcsFile->addFixableError('Expected %s', $closingBracket, 'Incorrect', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken(($closingBracket + 1), $comment.$phpcsFile->eolChar);
            }

            return;
        }

    }//end process()


}//end class
