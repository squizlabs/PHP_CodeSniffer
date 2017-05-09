<?php
/**
 * Ensure include_once is used in conditional situations and require_once is used elsewhere.
 *
 * Also checks that brackets do not surround the file being included.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Sniffs\Files;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class IncludingFileSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_INCLUDE_ONCE,
                T_REQUIRE_ONCE,
                T_REQUIRE,
                T_INCLUDE,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $nextToken = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($tokens[$nextToken]['code'] === T_OPEN_PARENTHESIS) {
            $error = '"%s" is a statement not a function; no parentheses are required';
            $data  = array($tokens[$stackPtr]['content']);
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'BracketsNotRequired', $data);
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($tokens[$nextToken]['parenthesis_closer'], '');
                if ($tokens[($nextToken - 1)]['code'] !== T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($nextToken, ' ');
                } else {
                    $phpcsFile->fixer->replaceToken($nextToken, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }

        if (count($tokens[$stackPtr]['conditions']) !== 0) {
            $inCondition = true;
        } else {
            $inCondition = false;
        }

        // Check to see if this including statement is within the parenthesis
        // of a condition. If that's the case then we need to process it as being
        // within a condition, as they are checking the return value.
        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            foreach ($tokens[$stackPtr]['nested_parenthesis'] as $left => $right) {
                if (isset($tokens[$left]['parenthesis_owner']) === true) {
                    $inCondition = true;
                }
            }
        }

        // Check to see if they are assigning the return value of this
        // including call. If they are then they are probably checking it, so
        // it's conditional.
        $previous = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if (isset(Tokens::$assignmentTokens[$tokens[$previous]['code']]) === true) {
            // The have assigned the return value to it, so its conditional.
            $inCondition = true;
        }

        $tokenCode = $tokens[$stackPtr]['code'];
        if ($inCondition === true) {
            // We are inside a conditional statement. We need an include_once.
            if ($tokenCode === T_REQUIRE_ONCE) {
                $error  = 'File is being conditionally included; ';
                $error .= 'use "include_once" instead';
                $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'UseIncludeOnce');
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($stackPtr, 'include_once');
                }
            } else if ($tokenCode === T_REQUIRE) {
                $error  = 'File is being conditionally included; ';
                $error .= 'use "include" instead';
                $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'UseInclude');
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($stackPtr, 'include');
                }
            }
        } else {
            // We are unconditionally including, we need a require_once.
            if ($tokenCode === T_INCLUDE_ONCE) {
                $error  = 'File is being unconditionally included; ';
                $error .= 'use "require_once" instead';
                $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'UseRequireOnce');
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($stackPtr, 'require_once');
                }
            } else if ($tokenCode === T_INCLUDE) {
                $error  = 'File is being unconditionally included; ';
                $error .= 'use "require" instead';
                $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'UseRequire');
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($stackPtr, 'require');
                }
            }
        }//end if

    }//end process()


}//end class
