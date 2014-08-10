<?php
/**
 * Verifies that class members are spaced correctly.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractVariableSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractVariableSniff not found');
}

/**
 * Verifies that class members are spaced correctly.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_WhiteSpace_MemberVarSpacingSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{


    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $ignore   = PHP_CodeSniffer_Tokens::$methodPrefixes;
        $ignore[] = T_WHITESPACE;

        $prev = $phpcsFile->findPrevious($ignore, ($stackPtr - 1), null, true);
        if (isset(PHP_CodeSniffer_Tokens::$commentTokens[$tokens[$prev]['code']]) === true) {
            // Assume the comment belongs to the member var.
            // Check the spacing, but then skip it.
            $foundLines = ($tokens[$stackPtr]['line'] - $tokens[$prev]['line'] - 1);
            if ($foundLines > 0) {
                $error = 'Expected 0 blank lines after member var comment; %s found';
                $data  = array($foundLines);
                $fix   = $phpcsFile->addFixableError($error, $prev, 'AfterComment', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($prev + 1); $i <= $stackPtr; $i++) {
                        if ($tokens[$i]['line'] === $tokens[$stackPtr]['line']) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->addNewline($prev);
                    $phpcsFile->fixer->endChangeset();
                }
            }//end if

            $start = $prev;
        } else {
            $start = $stackPtr;
        }//end if

        // There needs to be 1 blank line before the var, not counting comments.
        $prevLineToken = null;
        for ($i = ($start - 1); $i > 0; $i--) {
            if (isset(PHP_CodeSniffer_Tokens::$commentTokens[$tokens[$i]['code']]) === true) {
                // Skip comments.
                continue;
            } else if (strpos($tokens[$i]['content'], $phpcsFile->eolChar) === false) {
                // Not the end of the line.
                continue;
            } else {
                $prevLineToken = $i;
                break;
            }
        }

        if (is_null($prevLineToken) === true) {
            // Never found the previous line, which means
            // there are 0 blank lines before the member var.
            $foundLines = 0;
        } else {
            $prevContent = $phpcsFile->findPrevious(T_WHITESPACE, $prevLineToken, null, true);
            $foundLines  = ($tokens[$prevLineToken]['line'] - $tokens[$prevContent]['line']);
        }//end if

        if ($foundLines === 1) {
            return;
        }

        $error = 'Expected 1 blank line before member var; %s found';
        $data  = array($foundLines);
        $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'Incorrect', $data);
        if ($fix === true) {
            if ($foundLines === 0) {
                $phpcsFile->fixer->addNewline($prevLineToken);
            } else {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($prevContent + 1); $i <= $prevLineToken; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->addNewline($prevLineToken);
                $phpcsFile->fixer->endChangeset();
            }
        }//end if

    }//end processMemberVar()


    /**
     * Processes normal variables.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        /*
            We don't care about normal variables.
        */

    }//end processVariable()


    /**
     * Processes variables in double quoted strings.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        /*
            We don't care about normal variables.
        */

    }//end processVariableInString()


}//end class
