<?php
/**
 * Ensures that the value of a comparison is not assigned to a variable.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class DisallowComparisonAssignmentSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_EQUAL];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore default value assignments in function definitions.
        $function = $phpcsFile->findPrevious(T_FUNCTION, ($stackPtr - 1), null, false, null, true);
        if ($function !== false) {
            $opener = $tokens[$function]['parenthesis_opener'];
            $closer = $tokens[$function]['parenthesis_closer'];
            if ($opener < $stackPtr && $closer > $stackPtr) {
                return;
            }
        }

        // Ignore values in array definitions.
        $array = $phpcsFile->findNext(
            T_ARRAY,
            ($stackPtr + 1),
            null,
            false,
            null,
            true
        );

        if ($array !== false) {
            return;
        }

        // Ignore function calls.
        $ignore = [
            T_NULLSAFE_OBJECT_OPERATOR,
            T_OBJECT_OPERATOR,
            T_STRING,
            T_VARIABLE,
            T_WHITESPACE,
        ];

        $next = $phpcsFile->findNext($ignore, ($stackPtr + 1), null, true);
        if ($tokens[$next]['code'] === T_CLOSURE
            || ($tokens[$next]['code'] === T_OPEN_PARENTHESIS
            && $tokens[($next - 1)]['code'] === T_STRING)
        ) {
            // Code will look like: $var = myFunction(
            // and will be ignored.
            return;
        }

        $endStatement = $phpcsFile->findEndOfStatement($stackPtr);
        for ($i = ($stackPtr + 1); $i < $endStatement; $i++) {
            if ((isset(Tokens::$comparisonTokens[$tokens[$i]['code']]) === true
                && $tokens[$i]['code'] !== T_COALESCE)
                || $tokens[$i]['code'] === T_INLINE_THEN
            ) {
                $error = 'The value of a comparison must not be assigned to a variable';
                $phpcsFile->addError($error, $stackPtr, 'AssignedComparison');
                break;
            }

            if (isset(Tokens::$booleanOperators[$tokens[$i]['code']]) === true
                || $tokens[$i]['code'] === T_BOOLEAN_NOT
            ) {
                $error = 'The value of a boolean operation must not be assigned to a variable';
                $phpcsFile->addError($error, $stackPtr, 'AssignedBool');
                break;
            }
        }

    }//end process()


}//end class
