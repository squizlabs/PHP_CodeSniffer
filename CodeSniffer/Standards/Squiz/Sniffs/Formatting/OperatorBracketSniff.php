<?php
/**
 * Squiz_Sniffs_Formatting_OperationBracketSniff.
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
require_once 'PHP/CodeSniffer/Tokens.php';

/**
 * Squiz_Sniffs_Formatting_OperationBracketSniff.
 *
 * Tests that all arithmetic operations are bracketed.
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
class Squiz_Sniffs_Formatting_OperatorBracketSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$operators;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
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

        // If the & is a reference, then we don't want to check for brackets.
        if ($tokens[$stackPtr]['code'] === T_BITWISE_AND && $phpcsFile->isReference($stackPtr) === true) {
            return;
        }

        // There is one instance where brackets aren't needed, which involves
        // the minus sign being used to assign a negative number to a variable.
        if ($tokens[$stackPtr]['code'] === T_MINUS) {
            // Check to see if we are trying to return -n.
            $prev = $phpcsFile->findPrevious(array(T_WHITESPACE, T_COMMENT), ($stackPtr - 1), null, true);

            if ($tokens[$prev]['code'] === T_RETURN) {
                return;
            }

            $number = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            if ($tokens[$number]['code'] === T_LNUMBER) {
                $previous = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
                if ($previous !== false) {
                    $isAssignment = in_array($tokens[$previous]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens);
                    $isEquality   = in_array($tokens[$previous]['code'], PHP_CodeSniffer_Tokens::$equalityTokens);
                    if ($isAssignment === true || $isEquality === true) {
                        // This is a negative assignment or comparion.
                        // We need to check that the minus and the number are
                        // adjacent.
                        if (($number - $stackPtr) !== 1) {
                            $error = 'No space allowed between minus sign and number';
                            $phpcsFile->addError($error, $stackPtr);
                        }

                        return;
                    }
                }
            }
        }//end if

        $lastBracket = $stackPtr;
        while ($lastBracket !== false) {
            $lastBracket = $phpcsFile->findPrevious(T_OPEN_PARENTHESIS, ($lastBracket - 1), null, false, null, true);

            $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($lastBracket - 1), null, true);
            $prevCode  = $tokens[$prevToken]['code'];

            if ($prevCode === T_ISSET) {
                // The isset function has it's own token.
                continue;
            }

            if ($prevCode === T_STRING) {
                // This is a function call.
                continue;
            }

            if (in_array($prevCode, PHP_CodeSniffer_Tokens::$scopeOpeners) === true) {
                // This is a scope opener, like FOREACH or IF.
                continue;
            }

            if ($prevCode === T_OPEN_PARENTHESIS) {
                // These are two open parenthesis in a row. If the current
                // one doesn't enclose the operator, go to the previous one.
                if ($tokens[$lastBracket]['parenthesis_closer'] < $stackPtr) {
                    continue;
                }
            }

            break;
        }//end while

        if ($lastBracket === false) {
            // It is not in a bracketed statement at all.
            $previousToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true, null, true);
            if ($previousToken !== false) {
                // A list of tokens that indicate that the token is not
                // part of an arithmetic operation.
                $invalidTokens = array(
                                  T_COMMA,
                                  T_OPEN_PARENTHESIS,
                                  T_OPEN_SQUARE_BRACKET,
                                 );

                if (in_array($tokens[$previousToken]['code'], $invalidTokens) === false) {
                    $error = 'Arithmetic operation must be bracketed';
                    $phpcsFile->addError($error, $stackPtr);
                }

                return;
            }
        } else if ($tokens[$lastBracket]['parenthesis_closer'] < $stackPtr) {
            // There are a set of brackets in front of it that don't include it.
            $error = 'Arithmetic operation must be bracketed';
            $phpcsFile->addError($error, $stackPtr);
            return;
        } else {
            // We are enclosed in a set of bracket, so the last thing to
            // check is that we are not also enclosed in square brackets
            // like this: ($array[$index+1]), which is invalid.
            $squareBracket = $phpcsFile->findPrevious(T_OPEN_SQUARE_BRACKET, ($stackPtr - 1), $lastBracket);
            if ($squareBracket !== false) {
                $closeSquareBracket = $phpcsFile->findNext(T_CLOSE_SQUARE_BRACKET, ($stackPtr + 1));
                if ($closeSquareBracket !== false && $closeSquareBracket < $stackPtr) {
                    $error = 'Arithmetic operation must be bracketed';
                    $phpcsFile->addError($error, $stackPtr);
                }
            }

            return;
        }//end if

        $lastAssignment = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$assignmentTokens, $stackPtr, null, false, null, true);
        if ($lastAssignment !== false && $lastAssignment > $lastBracket) {
            $error = 'Arithmetic operation must be bracketed';
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end process()


}//end class

?>
