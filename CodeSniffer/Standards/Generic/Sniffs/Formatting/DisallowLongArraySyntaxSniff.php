<?php

/**
 * Generic_Sniffs_Formatting_DisallowLongArraySyntaxSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Xaver Loppenstedt <xaver@loppenstedt.de>
 * @copyright 2013-2015 Xaver Loppenstedt
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_Formatting_DisallowLongArraySyntaxSniff.
 *
 * Disallow long array syntax.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Xaver Loppenstedt <xaver@loppenstedt.de>
 * @copyright 2013-2015 Xaver Loppenstedt
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Formatting_DisallowLongArraySyntaxSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return array(int)
     */
    public function register()
    {
        return array(T_ARRAY);

    }//end register()


    /**
     * Called when one of the token types that this sniff is listening for
     * is found.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where the
     *                                        token was found.
     * @param int                  $stackPtr  The position in the PHP_CodeSniffer
     *                                        file's token stack where the token
     *                                        was found.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $token  = $tokens[$stackPtr];
        $opener = $token['parenthesis_opener'];
        $closer = $token['parenthesis_closer'];

        $errorMessage = 'Short array syntax must be used';

        if (($opener !== null) && ($closer !== null)) {
            $fix = $phpcsFile->addFixableError($errorMessage, $stackPtr);

            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($stackPtr, '');

                $phpcsFile->fixer->replaceToken($opener, '[');
                for ($i = ($stackPtr + 1); $i < $opener; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->replaceToken($closer, ']');

                $phpcsFile->fixer->endChangeset();
            }
        } else {
            // Don't fix erroneous arrays with!
            $phpcsFile->addError($errorMessage, $stackPtr);
        }

    }//end process()


}//end class
