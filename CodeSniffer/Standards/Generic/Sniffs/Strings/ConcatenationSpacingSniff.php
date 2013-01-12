<?php
/**
 * Generic_Sniffs_Strings_ConcatenationSpacingSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Roman Levishchenko <index.0h@gmail.com>
 * @copyright 2013 Roman Levishchenko
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_Strings_ConcatenationSpacingSniff.
 *
 * Makes sure there are no spaces between the concatenation operator (.) and
 * the strings being concatenated.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Roman Levishchenko <index.0h@gmail.com>
 * @copyright 2013 Roman Levishchenko
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Strings_ConcatenationSpacingSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING_CONCAT);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $message = 'Concat operator must be surrounded by 1 space';
        if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE
            && $tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE
        ) {
            $phpcsFile->addError($message, $stackPtr, 'Missing');
        } else {
            $message     = 'Concat operator must be surrounded by 1 space';
            $countBefore = strlen($tokens[($stackPtr - 1)]['content']);
            $countAfter  = strlen($tokens[($stackPtr + 1)]['content']);
            if ($countBefore > 1) {
                $phpcsFile->addError(
                    $message.'; found before '.$countBefore,
                    $stackPtr,
                    'Missing'
                );
            }
            if ($countAfter > 1) {
                $phpcsFile->addError(
                    $message.'; found after '.$countAfter,
                    $stackPtr,
                    'Missing'
                );
            }
        }
    }//end process()


}//end class