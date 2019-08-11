<?php
/**
 * Tests that files are not executable.
 *
 * @author    Matthew Peveler <matt.peveler@gmail.com>
 * @copyright 2019 Matthew Peveler
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Files;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class FileExtensionSniff implements Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_OPEN_TAG];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $perms = fileperms($phpcsFile->getFilename());

        if ($perms & 0x0040 || $perms & 0x0008 || $perms & 0x0001) {
            $error = "A PHP file must not be executable";
            $phpcsFile->addError($error, $stackPtr, 'Executable');
        }

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
