<?php
/**
 * Stops the usage of the "global" keyword.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class GlobalKeywordSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_GLOBAL);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $nextVar = $tokens[$phpcsFile->findNext(array(T_VARIABLE), $stackPtr)];
        $varName = str_replace('$', '', $nextVar['content']);
        $error   = 'Use of the "global" keyword is forbidden; use "$GLOBALS[\'%s\']" instead';
        $data    = array($varName);
        $phpcsFile->addError($error, $stackPtr, 'NotAllowed', $data);

    }//end process()


}//end class
