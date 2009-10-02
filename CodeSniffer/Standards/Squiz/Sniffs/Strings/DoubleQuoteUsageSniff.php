<?php
/**
 * Squiz_Sniffs_Strings_DoubleQuoteUsageSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_Strings_DoubleQuoteUsageSniff.
 *
 * Makes sure that any use of Double Quotes ("") are warranted.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Strings_DoubleQuoteUsageSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CONSTANT_ENCAPSED_STRING,
                T_DOUBLE_QUOTED_STRING,
               );

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
        $tokens = $phpcsFile->getTokens();

        // The use of variables in double quoted strings is not allowed.
        if ($tokens[$stackPtr]['code'] === T_DOUBLE_QUOTED_STRING) {
            $stringTokens = token_get_all('<?php '.$tokens[$stackPtr]['content']);
            foreach ($stringTokens as $token) {
                if (is_array($token) === true && $token[0] === T_VARIABLE) {
                    $error = 'Variable "'.$token[1].'" not allowed in double quoted string; use concatenation instead';
                    $phpcsFile->addError($error, $stackPtr);
                }
            }

            return;
        }//end if

        $workingString = $tokens[$stackPtr]['content'];

        // Check if it's a double quoted string.
        if (strpos($workingString, '"') === false) {
            return;
        }

        // Make sure it's not a part of a string started above.
        // If it is, then we have already checked it.
        if ($workingString[0] !== '"') {
            return;
        }

        // Work through the following tokens, in case this string is stretched
        // over multiple Lines.
        for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$i]['type'] !== 'T_CONSTANT_ENCAPSED_STRING') {
                break;
            }

            $workingString .= $tokens[$i]['content'];
        }

        $allowedChars = array(
                         '\0',
                         '\n',
                         '\r',
                         '\f',
                         '\t',
                         '\v',
                         '\x',
                         '\'',
                        );

        foreach ($allowedChars as $testChar) {
            if (strpos($workingString, $testChar) !== false) {
                return;
            }
        }

        $error = "String $workingString does not require double quotes; use single quotes instead";
        $phpcsFile->addError($error, $stackPtr);

    }//end process()


}//end class

?>
