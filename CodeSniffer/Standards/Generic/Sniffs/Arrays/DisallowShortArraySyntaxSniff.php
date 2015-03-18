<?php
/**
 * Bans the use of the PHP short array syntax.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Bans the use of the PHP short array syntax.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Arrays_DisallowShortArraySyntaxSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return array(T_OPEN_SHORT_ARRAY);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $phpcsFile->recordMetric($stackPtr, 'Short array syntax used', 'yes');

        $error = 'Short array syntax is not allowed';
        $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'Found');

        if ($fix === true) {
            $tokens = $phpcsFile->getTokens();
            $opener = $tokens[$stackPtr]['bracket_opener'];
            $closer = $tokens[$stackPtr]['bracket_closer'];

            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->replaceToken($opener, 'array(');
            $phpcsFile->fixer->replaceToken($closer, ')');
            $phpcsFile->fixer->endChangeset();
        }

    }//end process()


}//end class
