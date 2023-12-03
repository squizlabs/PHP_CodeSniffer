<?php
/**
 * Checks that all uses of TRUE, FALSE and NULL are uppercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;

class UpperCaseConstantSniff extends LowerCaseConstantSniff
{


    /**
     * Processes a non-type declaration constant.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    protected function processConstant(File $phpcsFile, $stackPtr)
    {
        $tokens   = $phpcsFile->getTokens();
        $keyword  = $tokens[$stackPtr]['content'];
        $expected = strtoupper($keyword);

        if ($keyword !== $expected) {
            if ($keyword === strtolower($keyword)) {
                $phpcsFile->recordMetric($stackPtr, 'PHP constant case', 'lower');
            } else {
                $phpcsFile->recordMetric($stackPtr, 'PHP constant case', 'mixed');
            }

            $error = 'TRUE, FALSE and NULL must be uppercase; expected "%s" but found "%s"';
            $data  = [
                $expected,
                $keyword,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Found', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, $expected);
            }
        } else {
            $phpcsFile->recordMetric($stackPtr, 'PHP constant case', 'upper');
        }

    }//end processConstant()


}//end class
