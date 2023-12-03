<?php
/**
 * Checks that only one class is declared per file.
 *
 * @author    Andy Grunwald <andygrunwald@gmail.com>
 * @copyright 2010-2014 Andy Grunwald
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class OneClassPerFileSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_CLASS];

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
        $tokens = $phpcsFile->getTokens();
        $start  = ($stackPtr + 1);
        if (isset($tokens[$stackPtr]['scope_closer']) === true) {
            $start = ($tokens[$stackPtr]['scope_closer'] + 1);
        }

        $nextClass = $phpcsFile->findNext($this->register(), $start);
        if ($nextClass !== false) {
            $error = 'Only one class is allowed in a file';
            $phpcsFile->addError($error, $nextClass, 'MultipleFound');
        }

    }//end process()


}//end class
