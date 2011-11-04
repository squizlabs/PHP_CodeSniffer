<?php
/**
 * Squiz_Sniffs_Arrays_ArrayBracketSpacingSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_Arrays_ArrayBracketSpacingSniff.
 *
 * Ensure that there are no spaces around square brackets.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Arrays_ArrayBracketSpacingSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_OPEN_SQUARE_BRACKET,
                T_CLOSE_SQUARE_BRACKET,
               );

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // No bracket can have space before them.
        $prevType = $tokens[($stackPtr - 1)]['code'];
        if (in_array($prevType, PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
            $nonSpace = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 2), null, true);
            $expected = $tokens[$nonSpace]['content'].$tokens[$stackPtr]['content'];
            $found    = $phpcsFile->getTokensAsString($nonSpace, ($stackPtr - $nonSpace)).$tokens[$stackPtr]['content'];
            $error    = 'Space found before square bracket; expected "%s" but found "%s"';
            $data     = array(
                         $expected,
                         $found,
                        );
            $phpcsFile->addError($error, $stackPtr, 'SpaceBeforeBracket', $data);
        }

        if ($tokens[$stackPtr]['type'] === 'T_OPEN_SQUARE_BRACKET') {
            // Open brackets can't have spaces on after them either.
            $nextType = $tokens[($stackPtr + 1)]['code'];
            if (in_array($nextType, PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                $nonSpace = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 2), null, true);
                $expected = $tokens[$stackPtr]['content'].$tokens[$nonSpace]['content'];
                $found    = $phpcsFile->getTokensAsString($stackPtr, ($nonSpace - $stackPtr + 1));
                $error    = 'Space found after square bracket; expected "%s" but found "%s"';
                $data     = array(
                             $expected,
                             $found,
                            );
                $phpcsFile->addError($error, $stackPtr, 'SpaceAfterBracket', $data);
            }
        }

    }//end process()


}//end class

?>
