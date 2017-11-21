<?php
/**
 * Checks that all uses of true, false and null are lowercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class LowerCaseConstantSniff implements Sniff
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
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_TRUE,
            T_FALSE,
            T_NULL,
        ];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens   = $phpcsFile->getTokens();
        $keyword  = $tokens[$stackPtr]['content'];
        $expected = strtolower($keyword);
        if ($keyword !== $expected) {
            if ($keyword === strtoupper($keyword)) {
                $phpcsFile->recordMetric($stackPtr, 'PHP constant case', 'upper');
            } else {
                $phpcsFile->recordMetric($stackPtr, 'PHP constant case', 'mixed');
            }

            $error = 'TRUE, FALSE and NULL must be lowercase; expected "%s" but found "%s"';
            $data  = [
                $expected,
                $keyword,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Found', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, $expected);
            }
        } else {
            $phpcsFile->recordMetric($stackPtr, 'PHP constant case', 'lower');
        }

    }//end process()


}//end class
