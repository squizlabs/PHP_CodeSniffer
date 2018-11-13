<?php
/**
 * Ensures all language constructs contain a single space between themselves and their content.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2017 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Util\Tokens;

class LanguageConstructSpacingSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_ECHO,
            T_PRINT,
            T_RETURN,
            T_INCLUDE,
            T_INCLUDE_ONCE,
            T_REQUIRE,
            T_REQUIRE_ONCE,
            T_NEW,
            T_YIELD,
            T_YIELD_FROM,
            T_THROW,
            T_NAMESPACE,
            T_USE,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($nextToken === false) {
            // Skip when at end of file.
            return;
        }

        if ($tokens[($stackPtr + 1)]['code'] === T_SEMICOLON) {
            // No content for this language construct.
            return;
        }

        $content = $tokens[$stackPtr]['content'];
        if ($tokens[$stackPtr]['code'] === T_NAMESPACE) {
            $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            if ($nextNonEmpty !== false && $tokens[$nextNonEmpty]['code'] === T_NS_SEPARATOR) {
                // Namespace keyword used as operator, not as the language construct.
                return;
            }
        }

        if ($tokens[$stackPtr]['code'] === T_YIELD_FROM
            && strtolower($content) !== 'yield from'
        ) {
            if ($tokens[($stackPtr - 1)]['code'] === T_YIELD_FROM) {
                // A multi-line statements that has already been processed.
                return;
            }

            $found = $content;
            if ($tokens[($stackPtr + 1)]['code'] === T_YIELD_FROM) {
                // This yield from statement is split over multiple lines.
                $i = ($stackPtr + 1);
                do {
                    $found .= $tokens[$i]['content'];
                    $i++;
                } while ($tokens[$i]['code'] === T_YIELD_FROM);
            }

            $error = 'Language constructs must be followed by a single space; expected 1 space between YIELD FROM found "%s"';
            $data  = [Common::prepareForOutput($found)];
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'IncorrectYieldFrom', $data);
            if ($fix === true) {
                preg_match('/yield/i', $found, $yield);
                preg_match('/from/i', $found, $from);
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($stackPtr, $yield[0].' '.$from[0]);

                if ($tokens[($stackPtr + 1)]['code'] === T_YIELD_FROM) {
                    $i = ($stackPtr + 1);
                    do {
                        $phpcsFile->fixer->replaceToken($i, '');
                        $i++;
                    } while ($tokens[$i]['code'] === T_YIELD_FROM);
                }

                $phpcsFile->fixer->endChangeset();
            }

            return;
        }//end if

        if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
            $content = $tokens[($stackPtr + 1)]['content'];
            if ($content !== ' ') {
                $error = 'Language constructs must be followed by a single space; expected 1 space but found "%s"';
                $data  = [Common::prepareForOutput($content)];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'IncorrectSingle', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                }
            }
        } else if ($tokens[($stackPtr + 1)]['code'] !== T_OPEN_PARENTHESIS) {
            $error = 'Language constructs must be followed by a single space; expected "%s" but found "%s"';
            $data  = [
                $tokens[$stackPtr]['content'].' '.$tokens[($stackPtr + 1)]['content'],
                $tokens[$stackPtr]['content'].$tokens[($stackPtr + 1)]['content'],
            ];
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'Incorrect', $data);
            if ($fix === true) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            }
        }//end if

    }//end process()


}//end class
