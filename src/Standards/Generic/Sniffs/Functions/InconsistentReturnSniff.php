<?php

/**
 * Ensures that all function paths return
 *
 * @author    Jesse G. Donat <donatj@gmail.com>
 * @copyright 2009-2014 Jesse G. Donat
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class InconsistentReturnSniff implements Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_FUNCTION
        ];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process( File $phpcsFile, $stackPtr )
    {
        $tokens = $phpcsFile->getTokens();

        $returnCount = 0;
        $funcLevel   = $tokens[$stackPtr]['level'];

        if (empty($tokens[$stackPtr]['scope_opener'])) {
            return;
        }

        $markerPtr = $tokens[$stackPtr]['scope_opener'];
        while (($markerPtr = $phpcsFile->findNext([ T_RETURN, T_THROW ], ($markerPtr + 1), $tokens[$stackPtr]['scope_closer'])) !== false) {

            $nextPtr = $phpcsFile->findNext(T_WHITESPACE, ($markerPtr + 1), null, true);
            if ($tokens[$nextPtr]['code'] === T_SEMICOLON) {
                return;
            }

            $markers[] = $markerPtr;

            $markerLevel = $tokens[$markerPtr]['level'];
            foreach ($tokens[$markerPtr]['conditions'] as $point => $code) {
                if (($code === T_FUNCTION || $code === T_CLOSURE) && $point > $stackPtr) {
                    continue 2;
                }

                if ($code === T_CATCH) {
                    $markerLevel--;
                }

                if ($code === T_SWITCH) {
                    $casePtr = $phpcsFile->findPrevious([ T_CASE, T_DEFAULT ], $markerPtr);
                    if ($casePtr && $tokens[$casePtr]['code'] === T_DEFAULT) {
                        $markerLevel--;
                    }
                }
            }

            if ($tokens[$markerPtr]['code'] === T_RETURN) {
                $returnCount++;
            }

            if ($markerLevel == $funcLevel + 1) {
                return;
            }
        }

        if ($returnCount == 0) {
            return;
        }

		$phpcsFile->addWarning("Not all paths through function return a value or throw exception.", $stackPtr, 'NotAllReturn');
    }

}