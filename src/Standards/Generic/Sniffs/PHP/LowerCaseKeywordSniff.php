<?php
/**
 * Checks that all PHP keywords are lowercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util;

class LowerCaseKeywordSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_ABSTRACT,
            T_ARRAY,
            T_AS,
            T_BREAK,
            T_CALLABLE,
            T_CASE,
            T_CATCH,
            T_CLASS,
            T_CLONE,
            T_CLOSURE,
            T_CONST,
            T_CONTINUE,
            T_DECLARE,
            T_DEFAULT,
            T_DO,
            T_ECHO,
            T_ELSE,
            T_ELSEIF,
            T_EMPTY,
            T_ENDDECLARE,
            T_ENDFOR,
            T_ENDFOREACH,
            T_ENDIF,
            T_ENDSWITCH,
            T_ENDWHILE,
            T_EVAL,
            T_EXIT,
            T_EXTENDS,
            T_FINAL,
            T_FINALLY,
            T_FN,
            T_FOR,
            T_FOREACH,
            T_FUNCTION,
            T_GLOBAL,
            T_GOTO,
            T_IF,
            T_IMPLEMENTS,
            T_INCLUDE,
            T_INCLUDE_ONCE,
            T_INSTANCEOF,
            T_INSTEADOF,
            T_INTERFACE,
            T_ISSET,
            T_LIST,
            T_LOGICAL_AND,
            T_LOGICAL_OR,
            T_LOGICAL_XOR,
            T_MATCH,
            T_MATCH_DEFAULT,
            T_NAMESPACE,
            T_NEW,
            T_PARENT,
            T_PRINT,
            T_PRIVATE,
            T_PROTECTED,
            T_PUBLIC,
            T_REQUIRE,
            T_REQUIRE_ONCE,
            T_RETURN,
            T_SELF,
            T_STATIC,
            T_SWITCH,
            T_THROW,
            T_TRAIT,
            T_TRY,
            T_UNSET,
            T_USE,
            T_VAR,
            T_WHILE,
            T_YIELD,
            T_YIELD_FROM,
        ];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens  = $phpcsFile->getTokens();
        $keyword = $tokens[$stackPtr]['content'];
        if (strtolower($keyword) !== $keyword) {
            if ($keyword === strtoupper($keyword)) {
                $phpcsFile->recordMetric($stackPtr, 'PHP keyword case', 'upper');
            } else {
                $phpcsFile->recordMetric($stackPtr, 'PHP keyword case', 'mixed');
            }

            $messageKeyword = Util\Common::prepareForOutput($keyword);

            $error = 'PHP keywords must be lowercase; expected "%s" but found "%s"';
            $data  = [
                strtolower($messageKeyword),
                $messageKeyword,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Found', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, strtolower($keyword));
            }
        } else {
            $phpcsFile->recordMetric($stackPtr, 'PHP keyword case', 'lower');
        }//end if

    }//end process()


}//end class
