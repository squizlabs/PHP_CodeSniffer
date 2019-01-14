<?php
/**
 * Ensure multi-line IF conditions are defined correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Sniffs\ControlStructures;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class MultiLineConditionSniff implements Sniff
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
     * The number of spaces code should be indented.
     *
     * @var integer
     */
    public $indent = 4;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_IF,
            T_ELSEIF,
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

        if (isset($tokens[$stackPtr]['parenthesis_opener']) === false) {
            return;
        }

        $openBracket    = $tokens[$stackPtr]['parenthesis_opener'];
        $closeBracket   = $tokens[$stackPtr]['parenthesis_closer'];
        $spaceAfterOpen = 0;
        if ($tokens[($openBracket + 1)]['code'] === T_WHITESPACE) {
            if (strpos($tokens[($openBracket + 1)]['content'], $phpcsFile->eolChar) !== false) {
                $spaceAfterOpen = 'newline';
            } else {
                $spaceAfterOpen = $tokens[($openBracket + 1)]['length'];
            }
        }

        if ($spaceAfterOpen !== 0) {
            $error = 'First condition of a multi-line IF statement must directly follow the opening parenthesis';
            $fix   = $phpcsFile->addFixableError($error, ($openBracket + 1), 'SpacingAfterOpenBrace');
            if ($fix === true) {
                if ($spaceAfterOpen === 'newline') {
                    $phpcsFile->fixer->replaceToken(($openBracket + 1), '');
                } else {
                    $phpcsFile->fixer->replaceToken(($openBracket + 1), '');
                }
            }
        }

        // We need to work out how far indented the if statement
        // itself is, so we can work out how far to indent conditions.
        $statementIndent = 0;
        for ($i = ($stackPtr - 1); $i >= 0; $i--) {
            if ($tokens[$i]['line'] !== $tokens[$stackPtr]['line']) {
                $i++;
                break;
            }
        }

        if ($i >= 0 && $tokens[$i]['code'] === T_WHITESPACE) {
            $statementIndent = $tokens[$i]['length'];
        }

        // Each line between the parenthesis should be indented 4 spaces
        // and start with an operator, unless the line is inside a
        // function call, in which case it is ignored.
        $prevLine = $tokens[$openBracket]['line'];
        for ($i = ($openBracket + 1); $i <= $closeBracket; $i++) {
            if ($i === $closeBracket && $tokens[$openBracket]['line'] !== $tokens[$i]['line']) {
                $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($i - 1), null, true);
                if ($tokens[$prev]['line'] === $tokens[$i]['line']) {
                    // Closing bracket is on the same line as a condition.
                    $error = 'Closing parenthesis of a multi-line IF statement must be on a new line';
                    $fix   = $phpcsFile->addFixableError($error, $closeBracket, 'CloseBracketNewLine');
                    if ($fix === true) {
                        // Account for a comment at the end of the line.
                        $next = $phpcsFile->findNext(T_WHITESPACE, ($closeBracket + 1), null, true);
                        if ($tokens[$next]['code'] !== T_COMMENT
                            && isset(Tokens::$phpcsCommentTokens[$tokens[$next]['code']]) === false
                        ) {
                            $phpcsFile->fixer->addNewlineBefore($closeBracket);
                        } else {
                            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true);
                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->replaceToken($closeBracket, '');
                            $phpcsFile->fixer->addContentBefore($next, ')');
                            $phpcsFile->fixer->endChangeset();
                        }
                    }
                }
            }//end if

            if ($tokens[$i]['line'] !== $prevLine) {
                if ($tokens[$i]['line'] === $tokens[$closeBracket]['line']) {
                    $next = $phpcsFile->findNext(T_WHITESPACE, $i, null, true);
                    if ($next !== $closeBracket) {
                        $expectedIndent = ($statementIndent + $this->indent);
                    } else {
                        // Closing brace needs to be indented to the same level
                        // as the statement.
                        $expectedIndent = $statementIndent;
                    }//end if
                } else {
                    $expectedIndent = ($statementIndent + $this->indent);
                }//end if

                if ($tokens[$i]['code'] === T_COMMENT
                    || isset(Tokens::$phpcsCommentTokens[$tokens[$i]['code']]) === true
                ) {
                    $prevLine = $tokens[$i]['line'];
                    continue;
                }

                // We changed lines, so this should be a whitespace indent token.
                if ($tokens[$i]['code'] !== T_WHITESPACE) {
                    $foundIndent = 0;
                } else {
                    $foundIndent = $tokens[$i]['length'];
                }

                if ($expectedIndent !== $foundIndent) {
                    $error = 'Multi-line IF statement not indented correctly; expected %s spaces but found %s';
                    $data  = [
                        $expectedIndent,
                        $foundIndent,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $i, 'Alignment', $data);
                    if ($fix === true) {
                        $spaces = str_repeat(' ', $expectedIndent);
                        if ($foundIndent === 0) {
                            $phpcsFile->fixer->addContentBefore($i, $spaces);
                        } else {
                            $phpcsFile->fixer->replaceToken($i, $spaces);
                        }
                    }
                }

                $next = $phpcsFile->findNext(Tokens::$emptyTokens, $i, null, true);
                if ($next !== $closeBracket && $tokens[$next]['line'] === $tokens[$i]['line']) {
                    if (isset(Tokens::$booleanOperators[$tokens[$next]['code']]) === false) {
                        $prev    = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($i - 1), $openBracket, true);
                        $fixable = true;
                        if (isset(Tokens::$booleanOperators[$tokens[$prev]['code']]) === false
                            && $phpcsFile->findNext(T_WHITESPACE, ($prev + 1), $next, true) !== false
                        ) {
                            // Condition spread over multi-lines interspersed with comments.
                            $fixable = false;
                        }

                        $error = 'Each line in a multi-line IF statement must begin with a boolean operator';
                        if ($fixable === false) {
                            $phpcsFile->addError($error, $next, 'StartWithBoolean');
                        } else {
                            $fix = $phpcsFile->addFixableError($error, $next, 'StartWithBoolean');
                            if ($fix === true) {
                                if (isset(Tokens::$booleanOperators[$tokens[$prev]['code']]) === true) {
                                    $phpcsFile->fixer->beginChangeset();
                                    $phpcsFile->fixer->replaceToken($prev, '');
                                    $phpcsFile->fixer->addContentBefore($next, $tokens[$prev]['content'].' ');
                                    $phpcsFile->fixer->endChangeset();
                                } else {
                                    for ($x = ($prev + 1); $x < $next; $x++) {
                                        $phpcsFile->fixer->replaceToken($x, '');
                                    }
                                }
                            }
                        }
                    }//end if
                }//end if

                $prevLine = $tokens[$i]['line'];
            }//end if

            if ($tokens[$i]['code'] === T_STRING) {
                $next = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), null, true);
                if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
                    // This is a function call, so skip to the end as they
                    // have their own indentation rules.
                    $i        = $tokens[$next]['parenthesis_closer'];
                    $prevLine = $tokens[$i]['line'];
                    continue;
                }
            }
        }//end for

        // From here on, we are checking the spacing of the opening and closing
        // braces. If this IF statement does not use braces, we end here.
        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        // The opening brace needs to be one space away from the closing parenthesis.
        $openBrace = $tokens[$stackPtr]['scope_opener'];
        $next      = $phpcsFile->findNext(T_WHITESPACE, ($closeBracket + 1), $openBrace, true);
        if ($next !== false) {
            // Probably comments in between tokens, so don't check.
            return;
        }

        if ($tokens[$openBrace]['line'] > $tokens[$closeBracket]['line']) {
            $length = -1;
        } else if ($openBrace === ($closeBracket + 1)) {
            $length = 0;
        } else if ($openBrace === ($closeBracket + 2)
            && $tokens[($closeBracket + 1)]['code'] === T_WHITESPACE
        ) {
            $length = $tokens[($closeBracket + 1)]['length'];
        } else {
            // Confused, so don't check.
            $length = 1;
        }

        if ($length === 1) {
            return;
        }

        $data = [$length];
        $code = 'SpaceBeforeOpenBrace';

        $error = 'There must be a single space between the closing parenthesis and the opening brace of a multi-line IF statement; found ';
        if ($length === -1) {
            $error .= 'newline';
            $code   = 'NewlineBeforeOpenBrace';
        } else {
            $error .= '%s spaces';
        }

        $fix = $phpcsFile->addFixableError($error, ($closeBracket + 1), $code, $data);
        if ($fix === true) {
            if ($length === 0) {
                $phpcsFile->fixer->addContent($closeBracket, ' ');
            } else {
                $phpcsFile->fixer->replaceToken(($closeBracket + 1), ' ');
            }
        }

    }//end process()


}//end class
