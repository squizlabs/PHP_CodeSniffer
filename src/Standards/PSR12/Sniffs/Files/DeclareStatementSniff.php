<?php
/**
 * Checks the format of the declare statements.
 *
 * @author    Sertan Danis <sdanis@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class DeclareStatementSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_DECLARE];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // Allow a byte-order mark.
        $tokens = $phpcsFile->getTokens();

        // There should be no space between declare keyword and opening parenthesis.
        $parenthesis = ($stackPtr + 1);
        if ($tokens[($stackPtr + 1)]['type'] !== 'T_OPEN_PARENTHESIS') {
            $parenthesis = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            $error       = 'Expected no space between declare keyword and opening parenthesis in a declare statement';

            if ($tokens[$parenthesis]['type'] === 'T_OPEN_PARENTHESIS') {
                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceFoundAfterDeclare');

                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), '');
                }
            } else {
                $phpcsFile->addError($error, $parenthesis, 'SpaceFoundAfterDeclare');
                $parenthesis = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($parenthesis + 1));
            }
        }

        // There should be no space between open parenthesis and the directive.
        $string = $phpcsFile->findNext(T_WHITESPACE, ($parenthesis + 1), null, true);
        if ($parenthesis !== false) {
            if ($tokens[($parenthesis + 1)]['type'] !== 'T_STRING') {
                $error = 'Expected no space between opening parenthesis and directive in a declare statement';

                if ($tokens[$string]['type'] === 'T_STRING') {
                    $fix = $phpcsFile->addFixableError($error, $parenthesis, 'SpaceFoundBeforeDirective');

                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($parenthesis + 1), '');
                    }
                } else {
                    $phpcsFile->addError($error, $string, 'SpaceFoundBeforeDirective');
                    $string = $phpcsFile->findNext(T_STRING, ($string + 1));
                }
            }
        }

        // There should be no space between directive and the equal sign.
        $equals = $phpcsFile->findNext(T_WHITESPACE, ($string + 1), null, true);
        if ($string !== false) {
            // The directive must be in lowercase.
            if ($tokens[$string]['content'] !== strtolower($tokens[$string]['content'])) {
                $error = 'The directive of a declare statement must be in lowercase';
                $fix   = $phpcsFile->addFixableError($error, $string, 'DirectiveNotLowercase');
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($string, strtolower($tokens[$string]['content']));
                }
            }

            if ($tokens[($string + 1)]['type'] !== 'T_EQUAL') {
                $error = 'Expected no space between directive and the equals sign in a declare statement';

                if ($tokens[$equals]['type'] === 'T_EQUAL') {
                    $fix = $phpcsFile->addFixableError($error, $equals, 'SpaceFoundAfterDirective');

                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($string + 1), '');
                    }
                } else {
                    $phpcsFile->addError($error, $equals, 'SpaceFoundAfterDirective');
                    $equals = $phpcsFile->findNext(T_EQUAL, ($equals + 1));
                }
            }
        }//end if

        // There should be no space between equal sign and directive value.
        $value = $phpcsFile->findNext(T_WHITESPACE, ($equals + 1), null, true);
        if ($equals !== false) {
            if ($tokens[($equals + 1)]['type'] !== 'T_LNUMBER') {
                $error = 'Expected no space between equal sign and the directive value in a declare statement';

                if ($tokens[$value]['type'] === 'T_LNUMBER') {
                    $fix = $phpcsFile->addFixableError($error, $value, 'SpaceFoundBeforeDirectiveValue');

                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($equals + 1), '');
                    }
                } else {
                    $phpcsFile->addError($error, $value, 'SpaceFoundBeforeDirectiveValue');
                    $value = $phpcsFile->findNext(T_LNUMBER, ($value + 1));
                }
            }
        }

        $parenthesis = $phpcsFile->findNext(T_WHITESPACE, ($value + 1), null, true);
        if ($value !== false) {
            if ($tokens[($value + 1)]['type'] !== 'T_CLOSE_PARENTHESIS') {
                $error = 'Expected no space between the directive value and closing parenthesis in a declare statement';

                if ($tokens[$parenthesis]['type'] === 'T_CLOSE_PARENTHESIS') {
                    $fix = $phpcsFile->addFixableError($error, $parenthesis, 'SpaceFoundAfterDirectiveValue');

                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($value + 1), '');
                    }
                } else {
                    $phpcsFile->addError($error, $parenthesis, 'SpaceFoundAfterDirectiveValue');
                    $parenthesis = $phpcsFile->findNext(T_CLOSE_PARENTHESIS, ($parenthesis + 1));
                }
            }
        }

        // Check for semicolon.
        $curlyBracket = false;
        if ($tokens[($parenthesis + 1)]['type'] !== 'T_SEMICOLON') {
            $token = $phpcsFile->findNext(T_WHITESPACE, ($parenthesis + 1), null, true);

            if ($tokens[$token]['type'] === 'T_OPEN_CURLY_BRACKET') {
                // Block declaration.
                $curlyBracket = $token;
            } else if ($tokens[$token]['type'] === 'T_SEMICOLON') {
                $error = 'Expected no space between the closing parenthesis and the semicolon in a declare statement';
                $fix   = $phpcsFile->addFixableError($error, $parenthesis, 'SpaceFoundBeforeSemicolon');
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($parenthesis + 1), '');
                }
            } else if ($tokens[$token]['type'] === 'T_CLOSE_TAG') {
                if ($tokens[($parenthesis)]['line'] !== $tokens[$token]['line']) {
                    // Close tag must be on the same line..
                    $error = 'The close tag must be on the same line as the declare statement';
                    $fix   = $phpcsFile->addFixableError($error, $parenthesis, 'CloseTagOnNewLine');
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($parenthesis + 1), ' ');
                    }
                }
            } else {
                $error = 'Expected no space between the closing parenthesis and the semicolon in a declare statement';
                $phpcsFile->addError($error, $parenthesis, 'SpaceFoundBeforeSemicolon');

                // See if there is a semicolon or curly bracket after this token.
                $token = $phpcsFile->findNext([T_WHITESPACE, T_COMMENT], ($token + 1), null, true);
                if ($tokens[$token]['type'] === 'T_OPEN_CURLY_BRACKET') {
                    $curlyBracket = $token;
                }
            }//end if
        }//end if

        if ($curlyBracket !== false) {
            $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($curlyBracket - 1), null, true);
            $error     = 'Expected one space between closing parenthesis and opening curly bracket in a declare statement';

            // The opening curly bracket must on the same line with a single space between closing bracket.
            if ($tokens[$prevToken]['type'] !== 'T_CLOSE_PARENTHESIS') {
                $phpcsFile->addError($error, $curlyBracket, 'ExtraSpaceFoundAfterBracket');
            } else if ($phpcsFile->getTokensAsString(($prevToken + 1), ($curlyBracket - $prevToken - 1)) !== ' ') {
                $fix = $phpcsFile->addFixableError($error, $curlyBracket, 'ExtraSpaceFoundAfterBracket');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken(($prevToken + 1), ' ');
                    $nextToken = ($prevToken + 2);
                    while ($nextToken !== $curlyBracket) {
                        $phpcsFile->fixer->replaceToken($nextToken, '');
                        $nextToken++;
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }//end if

            $closeCurlyBracket = $tokens[$curlyBracket]['bracket_closer'];

            $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($closeCurlyBracket - 1), null, true);
            $nextToken = $phpcsFile->findNext([T_WHITESPACE, T_COMMENT], ($closeCurlyBracket + 1), null, true);
            $line      = $tokens[$closeCurlyBracket]['line'];

            // The closing curly bracket must be on a new line.
            if ($tokens[$prevToken]['line'] === $line || $tokens[$nextToken]['line'] === $line) {
                if ($tokens[$prevToken]['line'] === $line) {
                    $error = 'The closing curly bracket of a declare statement must be on a new line';
                    $fix   = $phpcsFile->addFixableError($error, $prevToken, 'CurlyBracketNotOnNewLine');
                    if ($fix === true) {
                        $phpcsFile->fixer->addNewline($prevToken);
                    }
                }
            }//end if

            // Closing curly bracket must align with the declare keyword.
            if ($tokens[$stackPtr]['column'] !== $tokens[$closeCurlyBracket]['column']) {
                $error = 'The closing curly bracket of a declare statements must be aligned with the declare keyword';

                $fix = $phpcsFile->addFixableError($error, $closeCurlyBracket, 'CloseBracketNotAligned');
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($closeCurlyBracket - 1), str_repeat(' ', ($tokens[$stackPtr]['column'] - 1)));
                }
            }

            // The open curly bracket must be the last code on the line.
            $token = $phpcsFile->findNext(Tokens::$emptyTokens, ($curlyBracket + 1), null, true);
            if ($tokens[$curlyBracket]['line'] === $tokens[$token]['line']) {
                $error = 'The open curly bracket of a declare statement must be the last code on the line';
                $fix   = $phpcsFile->addFixableError($error, $token, 'CodeFoundAfterCurlyBracket');

                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($token - 1), null, true);

                    for ($i = ($prevToken + 1); $i < $token; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->addNewLineBefore($token);

                    $phpcsFile->fixer->endChangeset();
                }
            }
        }//end if

    }//end process()


}//end class
