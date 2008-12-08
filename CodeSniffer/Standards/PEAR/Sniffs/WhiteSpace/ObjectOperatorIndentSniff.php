<?php
/**
 * PEAR_Sniffs_WhiteSpace_ObjectOperatorIndentSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * PEAR_Sniffs_WhiteSpace_ObjectOperatorIndentSniff.
 *
 * Checks that object operators are indented 4 spaces if they are the first
 * thing on a line.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PEAR_Sniffs_WhiteSpace_ObjectOperatorIndentSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OBJECT_OPERATOR);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Make sure this is the first object operator in a chain of them.
        $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if ($prev === false || $tokens[$prev]['code'] !== T_VARIABLE) {
            return;
        }

        // Make sure this is a chained call.
        $next = $phpcsFile->findNext(
            T_OBJECT_OPERATOR,
            ($stackPtr + 1),
            null,
            false,
            null,
            true
        );

        if ($next === false) {
            // Not a chained call.
            return;
        }

        // Determine correct indent.
        for ($i = ($stackPtr - 1); $i >= 0; $i--) {
            if ($tokens[$i]['line'] !== $tokens[$stackPtr]['line']) {
                $i++;
                break;
            }
        }

        $requiredIndent = 0;
        if ($tokens[$i]['code'] === T_WHITESPACE) {
            $requiredIndent = strlen($tokens[$i]['content']);
        }

        $requiredIndent += 4;

        // Check indentation of each object operator in the chain.
        while ($next !== false) {
            // Make sure it starts a line, otherwise dont check indent.
            $indent = $tokens[($next - 1)];
            if ($indent['code'] === T_WHITESPACE) {
                if ($indent['line'] === $tokens[$next]['line']) {
                    $foundIndent = strlen($indent['content']);
                } else {
                    $foundIndent = 0;
                }

                if ($foundIndent !== $requiredIndent) {
                    $error = "Object operator not indented correctly; expected $requiredIndent spaces but found $foundIndent";
                    $phpcsFile->addError($error, $next);
                }
            }

            // It cant be the last thing on the line either.
            $content = $phpcsFile->findNext(T_WHITESPACE, ($next + 1), null, true);
            if ($tokens[$content]['line'] !== $tokens[$next]['line']) {
                $error = 'Object operator must be at the start of the line, not the end';
                $phpcsFile->addError($error, $next);
            }

            $next = $phpcsFile->findNext(
                T_OBJECT_OPERATOR,
                ($next + 1),
                null,
                false,
                null,
                true
            );
        }//end while

    }//end process()


}//end class

?>
