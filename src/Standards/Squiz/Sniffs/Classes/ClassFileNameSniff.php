<?php
/**
 * Tests that the file name and the name of the class contained within the file match.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ClassFileNameSniff implements Sniff
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
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $fullPath = basename($phpcsFile->getFilename());
        $fileName = substr($fullPath, 0, strrpos($fullPath, '.'));
        if ($fileName === '') {
            // No filename probably means STDIN, so we can't do this check.
            return;
        }

        $tokens  = $phpcsFile->getTokens();
        $decName = $phpcsFile->findNext(T_STRING, $stackPtr);

        if ($tokens[$decName]['content'] !== $fileName) {
            $error = '%s name doesn\'t match filename; expected "%s %s"';
            $data  = [
                ucfirst($tokens[$stackPtr]['content']),
                $tokens[$stackPtr]['content'],
                $fileName,
            ];
            $phpcsFile->addError($error, $stackPtr, 'NoMatch', $data);
        }

    }//end process()


}//end class
