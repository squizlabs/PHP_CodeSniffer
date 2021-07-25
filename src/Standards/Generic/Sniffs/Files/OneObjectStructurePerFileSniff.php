<?php
/**
 * Checks that only one object structure is declared per file.
 *
 * @author    Mponos George <gmponos@gmail.com>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class OneObjectStructurePerFileSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
            T_ENUM,
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
        $tokens = $phpcsFile->getTokens();
        $start  = ($stackPtr + 1);
        if (isset($tokens[$stackPtr]['scope_closer']) === true) {
            $start = ($tokens[$stackPtr]['scope_closer'] + 1);
        }

        $nextClass = $phpcsFile->findNext($this->register(), $start);
        if ($nextClass !== false) {
            $error = 'Only one object structure is allowed in a file';
            $phpcsFile->addError($error, $nextClass, 'MultipleFound');
        }

    }//end process()


}//end class
