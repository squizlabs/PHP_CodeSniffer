<?php
/**
 * Check & fix whitespace on the inside of arbitrary parentheses.
 *
 * Arbitrary parentheses are those which are not owned by a function (call), array or control structure.
 * Spacing on the outside is not checked on purpose as this would too easily conflict with other spacing rules.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ArbitraryParenthesesSpacingSniff implements Sniff
{

    /**
     * The number of spaces desired on the inside of the parentheses.
     *
     * @var integer
     */
    public $spacing = 0;

    /**
     * Allow newlines instead of spaces.
     *
     * @var boolean
     */
    public $ignoreNewlines = false;

    /**
     * Tokens which when they precede an open parenthesis indicate
     * that this is a type of structure this sniff should ignore.
     *
     * @var array
     */
    private $ignoreTokens = [];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $this->ignoreTokens = Tokens::$functionNameTokens;

        $this->ignoreTokens[T_VARIABLE]            = T_VARIABLE;
        $this->ignoreTokens[T_CLOSE_PARENTHESIS]   = T_CLOSE_PARENTHESIS;
        $this->ignoreTokens[T_CLOSE_CURLY_BRACKET] = T_CLOSE_CURLY_BRACKET;
        $this->ignoreTokens[T_CLOSE_SQUARE_BRACKET] = T_CLOSE_SQUARE_BRACKET;
        $this->ignoreTokens[T_CLOSE_SHORT_ARRAY]    = T_CLOSE_SHORT_ARRAY;

        $this->ignoreTokens[T_USE]        = T_USE;
        $this->ignoreTokens[T_DECLARE]    = T_DECLARE;
        $this->ignoreTokens[T_THROW]      = T_THROW;
        $this->ignoreTokens[T_YIELD]      = T_YIELD;
        $this->ignoreTokens[T_YIELD_FROM] = T_YIELD_FROM;
        $this->ignoreTokens[T_CLONE]      = T_CLONE;

        return [
            T_OPEN_PARENTHESIS,
            T_CLOSE_PARENTHESIS,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile All the tokens found in the document.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['parenthesis_owner']) === true) {
            // This parenthesis is owned by a function/control structure etc.
            return;
        }

        // More checking for the type of parenthesis we *don't* want to handle.
        $opener = $stackPtr;
        if ($tokens[$stackPtr]['code'] === T_CLOSE_PARENTHESIS) {
            if (isset($tokens[$stackPtr]['parenthesis_opener']) === false) {
                // Parse error.
                return;
            }

            $opener = $tokens[$stackPtr]['parenthesis_opener'];
        }

        $preOpener = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($opener - 1), null, true);
        if ($preOpener !== false
            && isset($this->ignoreTokens[$tokens[$preOpener]['code']]) === true
            && isset($tokens[$preOpener]['scope_condition']) === false
        ) {
            // Function or language construct call.
            return;
        }

        // Check for empty parentheses.
        if ($tokens[$stackPtr]['code'] === T_OPEN_PARENTHESIS
            && isset($tokens[$stackPtr]['parenthesis_closer']) === true
        ) {
            $nextNonEmpty = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            if ($nextNonEmpty === $tokens[$stackPtr]['parenthesis_closer']) {
                $phpcsFile->addWarning('Empty set of arbitrary parentheses found.', $stackPtr, 'FoundEmpty');

                return ($tokens[$stackPtr]['parenthesis_closer'] + 1);
            }
        }

        // Check the spacing on the inside of the parentheses.
        $this->spacing = (int) $this->spacing;

        if ($tokens[$stackPtr]['code'] === T_OPEN_PARENTHESIS
            && isset($tokens[($stackPtr + 1)], $tokens[($stackPtr + 2)]) === true
        ) {
            $nextToken = $tokens[($stackPtr + 1)];

            if ($nextToken['code'] !== T_WHITESPACE) {
                $inside = 0;
            } else {
                if ($tokens[($stackPtr + 2)]['line'] !== $tokens[$stackPtr]['line']) {
                    $inside = 'newline';
                } else {
                    $inside = $nextToken['length'];
                }
            }

            if ($this->spacing !== $inside
                && ($inside !== 'newline' || $this->ignoreNewlines === false)
            ) {
                $error = 'Expected %s space after open parenthesis; %s found';
                $data  = [
                    $this->spacing,
                    $inside,
                ];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterOpen', $data);

                if ($fix === true) {
                    $expected = '';
                    if ($this->spacing > 0) {
                        $expected = str_repeat(' ', $this->spacing);
                    }

                    if ($inside === 0) {
                        if ($expected !== '') {
                            $phpcsFile->fixer->addContent($stackPtr, $expected);
                        }
                    } else if ($inside === 'newline') {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($stackPtr + 2); $i < $phpcsFile->numTokens; $i++) {
                            if ($tokens[$i]['code'] !== T_WHITESPACE) {
                                break;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->replaceToken(($stackPtr + 1), $expected);
                        $phpcsFile->fixer->endChangeset();
                    } else {
                        $phpcsFile->fixer->replaceToken(($stackPtr + 1), $expected);
                    }
                }//end if
            }//end if
        }//end if

        if ($tokens[$stackPtr]['code'] === T_CLOSE_PARENTHESIS
            && isset($tokens[($stackPtr - 1)], $tokens[($stackPtr - 2)]) === true
        ) {
            $prevToken = $tokens[($stackPtr - 1)];

            if ($prevToken['code'] !== T_WHITESPACE) {
                $inside = 0;
            } else {
                if ($tokens[($stackPtr - 2)]['line'] !== $tokens[$stackPtr]['line']) {
                    $inside = 'newline';
                } else {
                    $inside = $prevToken['length'];
                }
            }

            if ($this->spacing !== $inside
                && ($inside !== 'newline' || $this->ignoreNewlines === false)
            ) {
                $error = 'Expected %s space before close parenthesis; %s found';
                $data  = [
                    $this->spacing,
                    $inside,
                ];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeClose', $data);

                if ($fix === true) {
                    $expected = '';
                    if ($this->spacing > 0) {
                        $expected = str_repeat(' ', $this->spacing);
                    }

                    if ($inside === 0) {
                        if ($expected !== '') {
                            $phpcsFile->fixer->addContentBefore($stackPtr, $expected);
                        }
                    } else if ($inside === 'newline') {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($stackPtr - 2); $i > 0; $i--) {
                            if ($tokens[$i]['code'] !== T_WHITESPACE) {
                                break;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->replaceToken(($stackPtr - 1), $expected);
                        $phpcsFile->fixer->endChangeset();
                    } else {
                        $phpcsFile->fixer->replaceToken(($stackPtr - 1), $expected);
                    }
                }//end if
            }//end if
        }//end if

    }//end process()


}//end class
