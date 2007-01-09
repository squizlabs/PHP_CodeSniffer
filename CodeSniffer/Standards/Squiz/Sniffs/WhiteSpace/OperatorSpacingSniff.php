<?php
/**
 * Sniffs_Squiz_WhiteSpace_OperatorSpacingSniff.
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
 * Sniffs_Squiz_WhiteSpace_OperatorSpacingSniff.
 *
 * Verifies that operators have valid spacing surrounding them.
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
class Squiz_Sniffs_WhiteSpace_OperatorSpacingSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_MINUS,
                T_PLUS,
                T_MULTIPLY,
                T_DIVIDE,
                T_MODULUS,
                T_BITWISE_AND,
                T_BITWISE_OR,
                T_CONCAT_EQUAL,
                T_DIV_EQUAL,
                T_MUL_EQUAL,
                T_MINUS_EQUAL,
                T_PLUS_EQUAL,
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

        if ($tokens[$stackPtr]['code'] === T_BITWISE_AND) {
            // If its not a reference, then we expect one space either side of the
            // bitwise operator.
            if ($phpcsFile->isReference($stackPtr) === false) {
                // Check there is one space before the & operator.
                if ($tokens[$stackPtr - 1]['code'] !== T_WHITESPACE) {
                    $error = 'Expected 1 space before "&" operator; 0 found';
                    $phpcsFile->addError($error, $stackPtr);
                } else {
                    if (strlen($tokens[$stackPtr - 1]['content']) !== 1) {
                        $found = strlen($tokens[$stackPtr - 1]['content']);
                        $error = "Expected 1 space before \"&\" operator; $found found";
                        $phpcsFile->addError($error, $stackPtr);
                    }
                }

                // Check there is one space after the & operator.
                if ($tokens[$stackPtr + 1]['code'] !== T_WHITESPACE) {
                    $error = 'Expected 1 space after "&" operator; 0 found';
                    $phpcsFile->addError($error, $stackPtr);
                } else {
                    if (strlen($tokens[$stackPtr + 1]['content']) !== 1) {
                        $found = strlen($tokens[$stackPtr + 1]['content']);
                        $error = "Expected 1 space after \"&\" operator; $found found";
                        $phpcsFile->addError($error, $stackPtr);
                    }
                }
            }
        } else {
            if ($tokens[$stackPtr]['code'] === T_MINUS) {
                // Check minus spacing, but make sure we aren't just assigning
                // a minus value or returning one.
                $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
                if ($tokens[$prev]['code'] === T_RETURN) {
                    // Just returning a negative value; eg. return -1.
                    return;
                }

                if (in_array($tokens[$prev]['code'], PHP_CodeSniffer_Tokens::$operators) == true) {
                    // Just trying to operate on a negative value; eg. ($var * -1).
                    return;
                }

                if (in_array($tokens[$prev]['code'], PHP_CodeSniffer_Tokens::$equalityTokens) == true) {
                    // Just trying to compare a negative value; eg. ($var === -1).
                    return;
                }

                // A list of tokens that indicate that the token is not
                // part of an arithmetic operation.
                $invalidTokens = array(
                                  T_COMMA,
                                  T_OPEN_PARENTHESIS,
                                  T_OPEN_SQUARE_BRACKET,
                                 );

                if (in_array($tokens[$prev]['code'], $invalidTokens) === true) {
                    // Just trying to use a ngeative value; eg. myFunction($var, -2).
                    return;
                }

                $number = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
                if ($tokens[$number]['code'] === T_LNUMBER) {
                    $semi = $phpcsFile->findNext(T_WHITESPACE, ($number + 1), null, true);
                    if ($tokens[$semi]['code'] === T_SEMICOLON) {
                        if ($prev !== false && (in_array($tokens[$prev]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens) === true)) {
                            // This is a negative assignment.
                            return;
                        }
                    }
                }
            }//end if

            $operator = $tokens[$stackPtr]['content'];

            if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE) {
                $error = "Expected 1 space before \"$operator\"; 0 found";
                $phpcsFile->addError($error, $stackPtr);
            } else if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
                $error = "Expected 1 space after \"$operator\"; 0 found";
                $phpcsFile->addError($error, $stackPtr);
            } else if (strlen($tokens[($stackPtr + 1)]['content']) !== 1) {
                $found = strlen($tokens[($stackPtr + 1)]['content']);
                $error = "Expected 1 space after \"$operator\"; $found found";
                $phpcsFile->addError($error, $stackPtr);
            } else if (strlen($tokens[($stackPtr - 1)]['content']) !== 1) {
                // Don't throw an error for assignments, because other standards allow
                // multiple spaces there to align multiple assignments.
                if (in_array($tokens[$stackPtr]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens) === false) {
                    $found = strlen($tokens[($stackPtr - 1)]['content']);
                    $error = "Expected 1 space before \"$operator\"; $found found";
                    $phpcsFile->addError($error, $stackPtr);
                }
            }

        }//end if

    }//end processSupplementary()


}//end class

?>
