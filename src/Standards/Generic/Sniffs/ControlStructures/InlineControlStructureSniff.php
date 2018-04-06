<?php
/**
 * Verifies that inline control statements are not present.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\ControlStructures;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class InlineControlStructureSniff implements Sniff
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
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var boolean
     */
    public $error = true;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_IF,
            T_ELSE,
            T_ELSEIF,
            T_FOREACH,
            T_WHILE,
            T_DO,
            T_SWITCH,
            T_FOR,
        ];

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

        if (isset($tokens[$stackPtr]['scope_opener']) === true) {
            $phpcsFile->recordMetric($stackPtr, 'Control structure defined inline', 'no');
            return;
        }

        // Ignore the ELSE in ELSE IF. We'll process the IF part later.
        if ($tokens[$stackPtr]['code'] === T_ELSE) {
            $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            if ($tokens[$next]['code'] === T_IF) {
                return;
            }
        }

        if ($tokens[$stackPtr]['code'] === T_WHILE) {
            // This could be from a DO WHILE, which doesn't have an opening brace.
            $lastContent = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($tokens[$lastContent]['code'] === T_CLOSE_CURLY_BRACKET) {
                $brace = $tokens[$lastContent];
                if (isset($brace['scope_condition']) === true) {
                    $condition = $tokens[$brace['scope_condition']];
                    if ($condition['code'] === T_DO) {
                        return;
                    }
                }
            }

            // In Javascript DO WHILE loops without curly braces are legal. This
            // is only valid if a single statement is present between the DO and
            // the WHILE. We can detect this by checking only a single semicolon
            // is present between them.
            if ($phpcsFile->tokenizerType === 'JS') {
                $lastDo        = $phpcsFile->findPrevious(T_DO, ($stackPtr - 1));
                $lastSemicolon = $phpcsFile->findPrevious(T_SEMICOLON, ($stackPtr - 1));
                if ($lastDo !== false && $lastSemicolon !== false && $lastDo < $lastSemicolon) {
                    $precedingSemicolon = $phpcsFile->findPrevious(T_SEMICOLON, ($lastSemicolon - 1));
                    if ($precedingSemicolon === false || $precedingSemicolon < $lastDo) {
                        return;
                    }
                }
            }
        }//end if

        // This is a control structure without an opening brace,
        // so it is an inline statement.
        if ($this->error === true) {
            $fix = $phpcsFile->addFixableError('Inline control structures are not allowed', $stackPtr, 'NotAllowed');
        } else {
            $fix = $phpcsFile->addFixableWarning('Inline control structures are discouraged', $stackPtr, 'Discouraged');
        }

        $phpcsFile->recordMetric($stackPtr, 'Control structure defined inline', 'yes');

        // Stop here if we are not fixing the error.
        if ($fix !== true) {
            return;
        }

        $phpcsFile->fixer->beginChangeset();
        if (isset($tokens[$stackPtr]['parenthesis_closer']) === true) {
            $closer = $tokens[$stackPtr]['parenthesis_closer'];
        } else {
            $closer = $stackPtr;
        }

        if ($tokens[($closer + 1)]['code'] === T_WHITESPACE
            || $tokens[($closer + 1)]['code'] === T_SEMICOLON
        ) {
            $phpcsFile->fixer->addContent($closer, ' {');
        } else {
            $phpcsFile->fixer->addContent($closer, ' { ');
        }

        $fixableScopeOpeners = $this->register();

        $lastNonEmpty = $closer;
        for ($end = ($closer + 1); $end < $phpcsFile->numTokens; $end++) {
            if ($tokens[$end]['code'] === T_SEMICOLON) {
                break;
            }

            if ($tokens[$end]['code'] === T_CLOSE_TAG) {
                $end = $lastNonEmpty;
                break;
            }

            if (in_array($tokens[$end]['code'], $fixableScopeOpeners) === true
                && isset($tokens[$end]['scope_opener']) === false
            ) {
                // The best way to fix nested inline scopes is middle-out.
                // So skip this one. It will be detected and fixed on a future loop.
                $phpcsFile->fixer->rollbackChangeset();
                return;
            }

            if (isset($tokens[$end]['scope_opener']) === true) {
                $type = $tokens[$end]['code'];
                $end  = $tokens[$end]['scope_closer'];
                if ($type === T_DO || $type === T_IF || $type === T_ELSEIF || $type === T_TRY) {
                    $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($end + 1), null, true);
                    if ($next === false) {
                        break;
                    }

                    $nextType = $tokens[$next]['code'];

                    // Let additional conditions loop and find their ending.
                    if (($type === T_IF
                        || $type === T_ELSEIF)
                        && ($nextType === T_ELSEIF
                        || $nextType === T_ELSE)
                    ) {
                        continue;
                    }

                    // Account for DO... WHILE conditions.
                    if ($type === T_DO && $nextType === T_WHILE) {
                        $end = $phpcsFile->findNext(T_SEMICOLON, ($next + 1));
                    }

                    // Account for TRY... CATCH statements.
                    if ($type === T_TRY && $nextType === T_CATCH) {
                        $end = $tokens[$next]['scope_closer'];
                    }
                }//end if

                if ($tokens[$end]['code'] !== T_END_HEREDOC
                    && $tokens[$end]['code'] !== T_END_NOWDOC
                ) {
                    break;
                }
            }//end if

            if (isset($tokens[$end]['parenthesis_closer']) === true) {
                $end          = $tokens[$end]['parenthesis_closer'];
                $lastNonEmpty = $end;
                continue;
            }

            if ($tokens[$end]['code'] !== T_WHITESPACE) {
                $lastNonEmpty = $end;
            }
        }//end for

        if ($end === $phpcsFile->numTokens) {
            $end = $lastNonEmpty;
        }

        $nextContent = $phpcsFile->findNext(Tokens::$emptyTokens, ($end + 1), null, true);
        if ($nextContent === false || $tokens[$nextContent]['line'] !== $tokens[$end]['line']) {
            // Looks for completely empty statements.
            $next = $phpcsFile->findNext(T_WHITESPACE, ($closer + 1), ($end + 1), true);
        } else {
            $next    = ($end + 1);
            $endLine = $end;
        }

        if ($next !== $end) {
            if ($nextContent === false || $tokens[$nextContent]['line'] !== $tokens[$end]['line']) {
                // Account for a comment on the end of the line.
                for ($endLine = $end; $endLine < $phpcsFile->numTokens; $endLine++) {
                    if (isset($tokens[($endLine + 1)]) === false
                        || $tokens[$endLine]['line'] !== $tokens[($endLine + 1)]['line']
                    ) {
                        break;
                    }
                }

                if (isset(Tokens::$commentTokens[$tokens[$endLine]['code']]) === false
                    && ($tokens[$endLine]['code'] !== T_WHITESPACE
                    || isset(Tokens::$commentTokens[$tokens[($endLine - 1)]['code']]) === false)
                ) {
                    $endLine = $end;
                }
            }

            if ($endLine !== $end) {
                $endToken     = $endLine;
                $addedContent = '';
            } else {
                $endToken     = $end;
                $addedContent = $phpcsFile->eolChar;

                if ($tokens[$end]['code'] !== T_SEMICOLON
                    && $tokens[$end]['code'] !== T_CLOSE_CURLY_BRACKET
                ) {
                    $phpcsFile->fixer->addContent($end, '; ');
                }
            }

            $next = $phpcsFile->findNext(T_WHITESPACE, ($endToken + 1), null, true);
            if ($next !== false
                && ($tokens[$next]['code'] === T_ELSE
                || $tokens[$next]['code'] === T_ELSEIF)
            ) {
                $phpcsFile->fixer->addContentBefore($next, '} ');
            } else {
                $indent = '';
                for ($first = $stackPtr; $first > 0; $first--) {
                    if ($tokens[$first]['column'] === 1) {
                        break;
                    }
                }

                if ($tokens[$first]['code'] === T_WHITESPACE) {
                    $indent = $tokens[$first]['content'];
                } else if ($tokens[$first]['code'] === T_INLINE_HTML
                    || $tokens[$first]['code'] === T_OPEN_TAG
                ) {
                    $addedContent = '';
                }

                $addedContent .= $indent.'}';
                if ($next !== false && $tokens[$endToken]['code'] === T_COMMENT) {
                    $addedContent .= $phpcsFile->eolChar;
                }

                $phpcsFile->fixer->addContent($endToken, $addedContent);
            }//end if
        } else {
            if ($nextContent === false || $tokens[$nextContent]['line'] !== $tokens[$end]['line']) {
                // Account for a comment on the end of the line.
                for ($endLine = $end; $endLine < $phpcsFile->numTokens; $endLine++) {
                    if (isset($tokens[($endLine + 1)]) === false
                        || $tokens[$endLine]['line'] !== $tokens[($endLine + 1)]['line']
                    ) {
                        break;
                    }
                }

                if ($tokens[$endLine]['code'] !== T_COMMENT
                    && ($tokens[$endLine]['code'] !== T_WHITESPACE
                    || $tokens[($endLine - 1)]['code'] !== T_COMMENT)
                ) {
                    $endLine = $end;
                }
            }

            if ($endLine !== $end) {
                $phpcsFile->fixer->replaceToken($end, '');
                $phpcsFile->fixer->addNewlineBefore($endLine);
                $phpcsFile->fixer->addContent($endLine, '}');
            } else {
                $phpcsFile->fixer->replaceToken($end, '}');
            }
        }//end if

        $phpcsFile->fixer->endChangeset();

    }//end process()


}//end class
