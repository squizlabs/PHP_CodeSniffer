<?php
/**
 * Checks that there is one empty line before the closing brace of a function.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\PSR12;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class FunctionClosingBraceSpaceSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['PHP'];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_FUNCTION,
            T_CLOSURE,
        ];

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

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            // Probably an interface method.
            return;
        }

        $closeBrace  = $tokens[$stackPtr]['scope_closer'];
        $prevContent = $phpcsFile->findPrevious(T_WHITESPACE, ($closeBrace - 1), null, true);

        $nestedFunction = false;
        if ($phpcsFile->hasCondition($stackPtr, [T_FUNCTION, T_CLOSURE]) === true
            || isset($tokens[$stackPtr]['nested_parenthesis']) === true
        ) {
            $nestedFunction = true;
        }

        $braceLine = $tokens[$closeBrace]['line'];
        $prevLine  = $tokens[$prevContent]['line'];
        $found     = ($braceLine - $prevLine - 1);

        if ($found > 0) {
            $error = 'Expected 0 blank lines before closing brace of nested function; %s found';
            $data  = [$found];
            $fix   = $phpcsFile->addFixableError($error, $closeBrace, 'SpacingBeforeNestedClose', $data);

            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $changeMade = false;
                for ($i = ($prevContent + 1); $i < $closeBrace; $i++) {
                    // Try and maintain indentation.
                    if ($tokens[$i]['line'] === ($braceLine - 1)) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                    $changeMade = true;
                }

                // Special case for when the last content contains the newline
                // token as well, like with a comment.
                if ($changeMade === false) {
                    $phpcsFile->fixer->replaceToken(($prevContent + 1), '');
                }

                $phpcsFile->fixer->endChangeset();
            }//end if
        }//end if

    }//end process()


}//end class
