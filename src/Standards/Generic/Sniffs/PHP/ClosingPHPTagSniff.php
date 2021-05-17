<?php
/**
 * Checks that open PHP tags are paired with closing tags.
 *
 * @author    Stefano Kowalke <blueduck@gmx.net>
 * @copyright 2010-2014 Stefano Kowalke
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ClosingPHPTagSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_OPEN_TAG,
            T_OPEN_TAG_WITH_ECHO,
        ];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $closeTag = $phpcsFile->findNext(T_CLOSE_TAG, $stackPtr);
        if ($closeTag === false) {
            $error = 'The PHP open tag does not have a corresponding PHP close tag';
            $phpcsFile->addError($error, $stackPtr, 'NotFound');
        }

    }//end process()


}//end class
