<?php
/**
 * Ensure single and multi-line function declarations are defined correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions;

use PHP_CodeSniffer\Standards\PEAR\Sniffs\Functions\FunctionDeclarationSniff as PEARFunctionDeclarationSniff;
use PHP_CodeSniffer\Util\Tokens;

class MultiLineFunctionDeclarationSniff extends PEARFunctionDeclarationSniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];


    /**
     * Determine if this is a multi-line function declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file being scanned.
     * @param int                         $stackPtr    The position of the current token
     *                                                 in the stack passed in $tokens.
     * @param int                         $openBracket The position of the opening bracket
     *                                                 in the stack passed in $tokens.
     * @param array                       $tokens      The stack of tokens that make up
     *                                                 the file.
     *
     * @return void
     */
    public function isMultiLineDeclaration($phpcsFile, $stackPtr, $openBracket, $tokens)
    {
        $bracketsToCheck = [$stackPtr => $openBracket];

        // Closures may use the USE keyword and so be multi-line in this way.
        if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
            $use = $phpcsFile->findNext(T_USE, ($tokens[$openBracket]['parenthesis_closer'] + 1), $tokens[$stackPtr]['scope_opener']);
            if ($use !== false) {
                $open = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($use + 1));
                if ($open !== false) {
                    $bracketsToCheck[$use] = $open;
                }
            }
        }

        foreach ($bracketsToCheck as $stackPtr => $openBracket) {
            // If the first argument is on a new line, this is a multi-line
            // function declaration, even if there is only one argument.
            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($openBracket + 1), null, true);
            if ($tokens[$next]['line'] !== $tokens[$stackPtr]['line']) {
                return true;
            }

            $closeBracket = $tokens[$openBracket]['parenthesis_closer'];

            $end = $phpcsFile->findEndOfStatement($openBracket + 1);
            while ($tokens[$end]['code'] === T_COMMA) {
                // If the next bit of code is not on the same line, this is a
                // multi-line function declaration.
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($end + 1), $closeBracket, true);
                if ($next === false) {
                    continue(2);
                }

                if ($tokens[$next]['line'] !== $tokens[$end]['line']) {
                    return true;
                }

                $end = $phpcsFile->findEndOfStatement($next);
            }

            // We've reached the last argument, so see if the next content
            // (should be the close bracket) is also on the same line.
            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($end + 1), $closeBracket, true);
            if ($next !== false && $tokens[$next]['line'] !== $tokens[$end]['line']) {
                return true;
            }
        }//end foreach

        return false;

    }//end isMultiLineDeclaration()


    /**
     * Processes single-line declarations.
     *
     * Just uses the Generic BSD-Allman brace sniff.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     * @param array                       $tokens    The stack of tokens that make up
     *                                               the file.
     *
     * @return void
     */
    public function processSingleLineDeclaration($phpcsFile, $stackPtr, $tokens)
    {
        // We do everything the parent sniff does, and a bit more because we
        // define multi-line declarations a bit differently.
        parent::processSingleLineDeclaration($phpcsFile, $stackPtr, $tokens);

        $openingBracket = $tokens[$stackPtr]['parenthesis_opener'];
        $closingBracket = $tokens[$stackPtr]['parenthesis_closer'];

        $prevNonWhiteSpace = $phpcsFile->findPrevious(T_WHITESPACE, ($closingBracket - 1), $openingBracket, true);
        if ($tokens[$prevNonWhiteSpace]['line'] !== $tokens[$closingBracket]['line']) {
            $error = 'There must not be a newline before the closing parenthesis of a single-line function declaration';

            if (isset(Tokens::$emptyTokens[$tokens[$prevNonWhiteSpace]['code']]) === true) {
                $phpcsFile->addError($error, $closingBracket, 'CloseBracketNewLine');
            } else {
                $fix = $phpcsFile->addFixableError($error, $closingBracket, 'CloseBracketNewLine');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($closingBracket - 1); $i > $openingBracket; $i--) {
                        if ($tokens[$i]['code'] !== T_WHITESPACE) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }
        }//end if

    }//end processSingleLineDeclaration()


    /**
     * Processes multi-line declarations.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     * @param array                       $tokens    The stack of tokens that make up
     *                                               the file.
     *
     * @return void
     */
    public function processMultiLineDeclaration($phpcsFile, $stackPtr, $tokens)
    {
        // We do everything the parent sniff does, and a bit more.
        parent::processMultiLineDeclaration($phpcsFile, $stackPtr, $tokens);

        $openBracket = $tokens[$stackPtr]['parenthesis_opener'];
        $this->processBracket($phpcsFile, $openBracket, $tokens, 'function');

        if ($tokens[$stackPtr]['code'] !== T_CLOSURE) {
            return;
        }

        $use = $phpcsFile->findNext(T_USE, ($tokens[$stackPtr]['parenthesis_closer'] + 1), $tokens[$stackPtr]['scope_opener']);
        if ($use === false) {
            return;
        }

        $openBracket = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($use + 1), null);
        $this->processBracket($phpcsFile, $openBracket, $tokens, 'use');

    }//end processMultiLineDeclaration()


    /**
     * Processes the contents of a single set of brackets.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file being scanned.
     * @param int                         $openBracket The position of the open bracket
     *                                                 in the stack passed in $tokens.
     * @param array                       $tokens      The stack of tokens that make up
     *                                                 the file.
     * @param string                      $type        The type of the token the brackets
     *                                                 belong to (function or use).
     *
     * @return void
     */
    public function processBracket($phpcsFile, $openBracket, $tokens, $type='function')
    {
        $errorPrefix = '';
        if ($type === 'use') {
            $errorPrefix = 'Use';
        }

        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];

        // The open bracket should be the last thing on the line.
        if ($tokens[$openBracket]['line'] !== $tokens[$closeBracket]['line']) {
            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($openBracket + 1), null, true);
            if ($tokens[$next]['line'] !== ($tokens[$openBracket]['line'] + 1)) {
                $error = 'The first parameter of a multi-line '.$type.' declaration must be on the line after the opening bracket';
                $fix   = $phpcsFile->addFixableError($error, $next, $errorPrefix.'FirstParamSpacing');
                if ($fix === true) {
                    if ($tokens[$next]['line'] === $tokens[$openBracket]['line']) {
                        $phpcsFile->fixer->addNewline($openBracket);
                    } else {
                        $phpcsFile->fixer->beginChangeset();
                        for ($x = $openBracket; $x < $next; $x++) {
                            if ($tokens[$x]['line'] === $tokens[$openBracket]['line']) {
                                continue;
                            }

                            if ($tokens[$x]['line'] === $tokens[$next]['line']) {
                                break;
                            }
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }
            }//end if
        }//end if

        // Each line between the brackets should contain a single parameter.
        $lastComma = null;
        for ($i = ($openBracket + 1); $i < $closeBracket; $i++) {
            // Skip brackets, like arrays, as they can contain commas.
            if (isset($tokens[$i]['bracket_opener']) === true) {
                $i = $tokens[$i]['bracket_closer'];
                continue;
            }

            if (isset($tokens[$i]['parenthesis_opener']) === true) {
                $i = $tokens[$i]['parenthesis_closer'];
                continue;
            }

            if ($tokens[$i]['code'] !== T_COMMA) {
                continue;
            }

            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), null, true);
            if ($tokens[$next]['line'] === $tokens[$i]['line']) {
                $error = 'Multi-line '.$type.' declarations must define one parameter per line';
                $fix   = $phpcsFile->addFixableError($error, $next, $errorPrefix.'OneParamPerLine');
                if ($fix === true) {
                    $phpcsFile->fixer->addNewline($i);
                }
            }
        }//end for

    }//end processBracket()


}//end class
