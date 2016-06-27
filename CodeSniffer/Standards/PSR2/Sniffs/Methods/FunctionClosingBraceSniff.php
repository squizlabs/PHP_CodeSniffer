<?php
/**
 * PSR2_Sniffs_Methods_FunctionClosingBraceSniff.
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
 * PSR2_Sniffs_Methods_FunctionClosingBraceSniff.
 *
 * Checks that the closing brace of a function goes directly after the body.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PSR2_Sniffs_Methods_FunctionClosingBraceSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_FUNCTION,
                T_CLOSURE,
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

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            // Probably an interface method.
            return;
        }

        $closeBrace  = $tokens[$stackPtr]['scope_closer'];
        $prevContent = $phpcsFile->findPrevious(T_WHITESPACE, ($closeBrace - 1), null, true);
        $found       = ($tokens[$closeBrace]['line'] - $tokens[$prevContent]['line'] - 1);

        if ($found < 0) {
            // Brace isn't on a new line, so not handled by us.
            return;
        }

        if ($found === 0) {
            // All is good.
            return;
        }

        $error = 'Function closing brace must go on the next line following the body; found %s blank lines before brace';
        $data  = array($found);
        $fix   = $phpcsFile->addFixableError($error, $closeBrace, 'SpacingBeforeClose', $data);

        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();
            for ($i = ($prevContent + 1); $i < $closeBrace; $i++) {
                if ($tokens[$i]['line'] === $tokens[$prevContent]['line']) {
                    continue;
                }

                // Don't remove any identation before the brace.
                if ($tokens[$i]['line'] === $tokens[$closeBrace]['line']) {
                    break;
                }

                $phpcsFile->fixer->replaceToken($i, '');
            }

            $phpcsFile->fixer->endChangeset();
        }

    }//end process()


}//end class
