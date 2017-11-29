<?php
/**
 * Checks the declaration of the class is correct.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Sniffs\Classes;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class ClassDeclarationSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param integer                     $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $errorData = [strtolower($tokens[$stackPtr]['content'])];

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            $error = 'Possible parse error: %s missing opening or closing brace';
            $phpcsFile->addWarning($error, $stackPtr, 'MissingBrace', $errorData);
            return;
        }

        $curlyBrace  = $tokens[$stackPtr]['scope_opener'];
        $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($curlyBrace - 1), $stackPtr, true);
        $classLine   = $tokens[$lastContent]['line'];
        $braceLine   = $tokens[$curlyBrace]['line'];
        if ($braceLine === $classLine) {
            $phpcsFile->recordMetric($stackPtr, 'Class opening brace placement', 'same line');
            $error = 'Opening brace of a %s must be on the line after the definition';
            $fix   = $phpcsFile->addFixableError($error, $curlyBrace, 'OpenBraceNewLine', $errorData);
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                if ($tokens[($curlyBrace - 1)]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken(($curlyBrace - 1), '');
                }

                $phpcsFile->fixer->addNewlineBefore($curlyBrace);
                $phpcsFile->fixer->endChangeset();
            }

            return;
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Class opening brace placement', 'new line');

            if ($braceLine > ($classLine + 1)) {
                $error = 'Opening brace of a %s must be on the line following the %s declaration; found %s line(s)';
                $data  = [
                    $tokens[$stackPtr]['content'],
                    $tokens[$stackPtr]['content'],
                    ($braceLine - $classLine - 1),
                ];
                $fix   = $phpcsFile->addFixableError($error, $curlyBrace, 'OpenBraceWrongLine', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($curlyBrace - 1); $i > $lastContent; $i--) {
                        if ($tokens[$i]['line'] === ($tokens[$curlyBrace]['line'] + 1)) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }

                return;
            }//end if
        }//end if

        if ($tokens[($curlyBrace + 1)]['content'] !== $phpcsFile->eolChar) {
            $error = 'Opening %s brace must be on a line by itself';
            $fix   = $phpcsFile->addFixableError($error, $curlyBrace, 'OpenBraceNotAlone', $errorData);
            if ($fix === true) {
                $phpcsFile->fixer->addNewline($curlyBrace);
            }
        }

        if ($tokens[($curlyBrace - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($curlyBrace - 1)]['content'];
            if ($prevContent === $phpcsFile->eolChar) {
                $spaces = 0;
            } else {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = strlen($blankSpace);
            }

            $first    = $phpcsFile->findFirstOnLine(T_WHITESPACE, $stackPtr, true);
            $expected = ($tokens[$first]['column'] - 1);
            if ($spaces !== $expected) {
                $error = 'Expected %s spaces before opening brace; %s found';
                $data  = [
                    $expected,
                    $spaces,
                ];

                $fix = $phpcsFile->addFixableError($error, $curlyBrace, 'SpaceBeforeBrace', $data);
                if ($fix === true) {
                    $indent = str_repeat(' ', $expected);
                    if ($spaces === 0) {
                        $phpcsFile->fixer->addContentBefore($curlyBrace, $indent);
                    } else {
                        $phpcsFile->fixer->replaceToken(($curlyBrace - 1), $indent);
                    }
                }
            }
        }//end if

    }//end process()


}//end class
