<?php
/**
 * Squiz_Sniffs_Formatting_FunctionSpacingSniff.
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

require_once 'PHP/CodeSniffer/Sniff.php';

/**
 * Squiz_Sniffs_WhiteSpace_FunctionSpacingSniff.
 *
 * Checks the separation between methods in a class or interface.
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
class Squiz_Sniffs_WhiteSpace_FunctionSpacingSniff implements PHP_CodeSniffer_Sniff
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
               );

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        /*
            Check the number of blank lines
            after the function.
        */

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            // Must be an interface method, so the closer is the semi-colon.
            $closer = $phpcsFile->findNext(T_SEMICOLON, $stackPtr);
        } else {
            $closer = $tokens[$stackPtr]['scope_closer'];
        }

        // There needs to be 2 blank lines after the closer.
        $numTokens     = count($tokens);
        $nextLineToken = null;
        for ($i = $closer; $i < $numTokens; $i++) {
            if (strpos($tokens[$i]['content'], "\n") === false) {
                continue;
            } else {
                $nextLineToken = ($i + 1);
                break;
            }
        }
        
        if (is_null($nextLineToken) === true) {
            // Never found the next line, which means
            // there are 0 blank lines after the function.
            $foundLines = 0;
        } else {
            $nextContent = $phpcsFile->findNext(array(T_WHITESPACE), ($nextLineToken + 1), null, true);
            $foundLines  = $tokens[$nextContent]['line'] - $tokens[$nextLineToken]['line'];
        }

        if ($foundLines !== 2) {
            $phpcsFile->addError("Expected 2 blank lines after method; $foundLines line(s) found", $stackPtr);
        }

        /*
            Check the number of blank lines
            before the function.
        */

        $prevLineToken = null;
        for ($i = $stackPtr; $i > 0; $i--) {
            if (strpos($tokens[$i]['content'], "\n") === false) {
                continue;
            } else {
                $prevLineToken = $i;
                break;
            }
        }

        if (is_null($prevLineToken) === true) {
            // Never found the previous line, which means
            // there are 0 blank lines before the function.
            $foundLines = 0;
        } else {
            $prevContent = $phpcsFile->findPrevious(array(T_WHITESPACE, T_DOC_COMMENT), $prevLineToken, null, true);
            $foundLines  = $tokens[$prevLineToken]['line'] - $tokens[$prevContent]['line'];

            // Before we throw an error, check that we are not throwing an error
            // for another function. We don't want to error for no blank lines after
            // the previous function and no blank line before this one.
            $currentLine = $tokens[$prevContent]['line'];
            $prevLine    = ($currentLine - 1);
            $i           = ($stackPtr - 1);
            while ($currentLine != $prevLine) {
                if (isset($tokens[$i]['scope_condition']) === true) {
                    $scopeCondition = $tokens[$i]['scope_condition'];
                    if ($tokens[$scopeCondition]['code'] === T_FUNCTION) {
                        // Found a previous function.
                        return;
                    }
                } else if ($tokens[$i]['code'] === T_FUNCTION) {
                    // Found another interface function.
                    return;
                }

                $currentLine = $tokens[$i]['line'];
                $i--;
            }
        }

        if ($foundLines !== 2) {
            $phpcsFile->addError("Expected 2 blank lines before method; $foundLines line(s) found", $stackPtr);
        }

    }//end processTokenWithinScope()


}//end class

?>
