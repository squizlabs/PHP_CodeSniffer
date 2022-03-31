<?php
/**
 * Ensures class and interface names start with a capital letter and use _ separators.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ValidClassNameSniff implements Sniff
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
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being processed.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $className = $phpcsFile->findNext(T_STRING, $stackPtr);
        $name      = trim($tokens[$className]['content']);
        $errorData = [ucfirst($tokens[$stackPtr]['content'])];

        // Make sure the first letter is a capital.
        if (preg_match('|^[A-Z]|', $name) === 0) {
            $error = '%s name must begin with a capital letter';
            $phpcsFile->addError($error, $stackPtr, 'StartWithCapital', $errorData);
        }

        // Check that each new word starts with a capital as well, but don't
        // check the first word, as it is checked above.
        $validName = true;
        $nameBits  = explode('_', $name);
        $firstBit  = array_shift($nameBits);
        foreach ($nameBits as $bit) {
            if ($bit === '' || $bit[0] !== strtoupper($bit[0])) {
                $validName = false;
                break;
            }
        }

        if ($validName === false) {
            // Strip underscores because they cause the suggested name
            // to be incorrect.
            $nameBits = explode('_', trim($name, '_'));
            $firstBit = array_shift($nameBits);
            if ($firstBit === '') {
                $error = '%s name is not valid';
                $phpcsFile->addError($error, $stackPtr, 'Invalid', $errorData);
            } else {
                $newName = strtoupper($firstBit[0]).substr($firstBit, 1).'_';
                foreach ($nameBits as $bit) {
                    if ($bit !== '') {
                        $newName .= strtoupper($bit[0]).substr($bit, 1).'_';
                    }
                }

                $newName = rtrim($newName, '_');
                $error   = '%s name is not valid; consider %s instead';
                $data    = $errorData;
                $data[]  = $newName;
                $phpcsFile->addError($error, $stackPtr, 'Invalid', $data);
            }
        }//end if

    }//end process()


}//end class
