<?php
/**
 * Ensures function params with default values are at the end of the declaration.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ValidDefaultValueSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [
            T_FUNCTION,
            T_CLOSURE,
            T_FN,
        ];

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
        // Flag for when we have found a default in our arg list.
        // If there is a value without a default after this, it is an error.
        $defaultFound = false;

        $params = $phpcsFile->getMethodParameters($stackPtr);
        foreach ($params as $param) {
            if ($param['variable_length'] === true) {
                continue;
            }

            if (array_key_exists('default', $param) === true) {
                $defaultFound = true;
                // Check if the arg is type hinted and using NULL for the default.
                // This does not make the argument optional - it just allows NULL
                // to be passed in.
                if ($param['type_hint'] !== '' && strtolower($param['default']) === 'null') {
                    $defaultFound = false;
                }

                continue;
            }

            if ($defaultFound === true) {
                $error = 'Arguments with default values must be at the end of the argument list';
                $phpcsFile->addError($error, $param['token'], 'NotAtEnd');
                return;
            }
        }//end foreach

    }//end process()


}//end class
