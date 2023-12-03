<?php
/**
 * Tests that classes and interfaces are not declared in .php files.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

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
        $tokens    = $phpcsFile->getTokens();
        $fileName  = $phpcsFile->getFilename();
        $extension = substr($fileName, strrpos($fileName, '.'));
        $nextClass = $phpcsFile->findNext([T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM], $stackPtr);

        if ($nextClass !== false) {
            $phpcsFile->recordMetric($stackPtr, 'File extension for class files', $extension);
            if ($extension === '.php') {
                $error = '%s found in ".php" file; use ".inc" extension instead';
                $data  = [ucfirst($tokens[$nextClass]['content'])];
                $phpcsFile->addError($error, $stackPtr, 'ClassFound', $data);
            }
        } else {
            $phpcsFile->recordMetric($stackPtr, 'File extension for non-class files', $extension);
            if ($extension === '.inc') {
                $error = 'No interface or class found in ".inc" file; use ".php" extension instead';
                $phpcsFile->addError($error, $stackPtr, 'NoClass');
            }
        }

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
