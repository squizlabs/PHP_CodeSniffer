<?php
/**
 * Verifies that trait import statements are defined correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Traits;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class UseDeclarationSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_USE];

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

        // Needs to be a use statement directly inside a class.
        $conditions = $tokens[$stackPtr]['conditions'];
        end($conditions);
        if (isset(Tokens::$ooScopeTokens[current($conditions)]) === false) {
            return;
        }

        // If this is the first use statement inside the class, it needs
        // to be defined right after the opening brace.
        $ooToken  = key($conditions);
        $opener   = $tokens[$ooToken]['scope_opener'];
        $firstUse = $opener;
        do {
            $firstUse = $phpcsFile->findNext(T_USE, ($firstUse + 1), $tokens[$ooToken]['scope_closer']);
            if ($firstUse === false) {
                break;
            }

            if ($tokens[$firstUse]['conditions'] !== $conditions) {
                continue;
            }

            break;
        } while ($firstUse !== false);

        if ($firstUse === $stackPtr) {
            // The first non-comment line must be the use line.
            $lastValidContent = $stackPtr;
            for ($i = ($stackPtr - 1); $i > $opener; $i--) {
                if ($tokens[$i]['code'] === T_WHITESPACE
                    && ($tokens[($i - 1)]['line'] === $tokens[$i]['line']
                    || $tokens[($i + 1)]['line'] === $tokens[$i]['line'])
                ) {
                    continue;
                }

                if (isset(Tokens::$commentTokens[$tokens[$i]['code']]) === true) {
                    if ($tokens[$i]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
                        // Skip past the comment.
                        $i = $tokens[$i]['comment_opener'];
                    }

                    $lastValidContent = $i;
                }

                break;
            }

            if ($tokens[$lastValidContent]['line'] !== ($tokens[$opener]['line'] + 1)) {
                $error = 'The first trait import statement must be declared on the first non-comment line after the %s opening brace';
                $data  = [strtolower($tokens[$ooToken]['content'])];

                // Figure out if we can fix this error.
                $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), ($opener - 1), true);
                if ($tokens[$prev]['line'] === $tokens[$opener]['line']) {
                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'UseAfterBrace', $data);
                    if ($fix === true) {
                        // We know that the USE statements is the first non-comment content
                        // in the class, so we just need to remove blank lines.
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($stackPtr - 1); $i > $opener; $i--) {
                            if ($tokens[$i]['line'] === $tokens[$opener]['line']) {
                                break;
                            }

                            if ($tokens[$i]['line'] === $tokens[$stackPtr]['line']) {
                                continue;
                            }

                            if ($tokens[$i]['code'] === T_WHITESPACE
                                && $tokens[($i - 1)]['line'] !== $tokens[$i]['line']
                                && $tokens[($i + 1)]['line'] !== $tokens[$i]['line']
                            ) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            if (isset(Tokens::$commentTokens[$tokens[$i]['code']]) === true) {
                                if ($tokens[$i]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
                                    // Skip past the comment.
                                    $i = $tokens[$i]['comment_opener'];
                                }

                                $lastValidContent = $i;
                            }
                        }//end for

                        $phpcsFile->fixer->endChangeset();
                    }//end if
                } else {
                    $phpcsFile->addError($error, $stackPtr, 'UseAfterBrace', $data);
                }//end if
            }//end if
        } else {
            // Make sure this use statement immediately follows the previous one.
            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($prev !== false && $tokens[$prev]['line'] !== ($tokens[$stackPtr]['line'] - 1)) {
                $error     = 'Each imported trait must be on the line after the previous import';
                $prevNonWs = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
                if ($prevNonWs !== $prev) {
                    $phpcsFile->addError($error, $stackPtr, 'SpacingBeforeImport');
                } else {
                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBeforeImport');
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($x = ($stackPtr - 1); $x > $prev; $x--) {
                            if ($tokens[$stackPtr]['line'] > $tokens[$firstUse]['line']
                                && $tokens[$x]['line'] === $tokens[$stackPtr]['line']
                            ) {
                                // Preserve indent.
                                continue;
                            }

                            $phpcsFile->fixer->replaceToken($x, '');
                        }

                        $phpcsFile->fixer->addNewline($prev);
                        if ($tokens[$prev]['line'] === $tokens[$stackPtr]['line']) {
                            if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
                                $phpcsFile->fixer->replaceToken(($stackPtr - 1), '');
                            }

                            $padding = str_repeat(' ', ($tokens[$firstUse]['column'] - 1));
                            $phpcsFile->fixer->addContent($prev, $padding);
                        }

                        $phpcsFile->fixer->endChangeset();
                    }//end if
                }//end if
            }//end if
        }//end if

        if (isset($tokens[$stackPtr]['scope_opener']) === true) {
            $this->processUseGroup($phpcsFile, $stackPtr);
            $end = $tokens[$stackPtr]['scope_closer'];
        } else {
            $this->processUseStatement($phpcsFile, $stackPtr);
            $end = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
            if ($end === false) {
                // Syntax error.
                return;
            }
        }

        $next = $phpcsFile->findNext(T_WHITESPACE, ($end + 1), null, true);
        if ($next === $tokens[$ooToken]['scope_closer']) {
            // Last content in the class.
            $closer = $tokens[$ooToken]['scope_closer'];
            if ($tokens[$closer]['line'] > ($tokens[$end]['line'] + 1)) {
                $error = 'There must be no blank line after the last trait import statement at the bottom of a %s';
                $data  = [strtolower($tokens[$ooToken]['content'])];
                $fix   = $phpcsFile->addFixableError($error, $end, 'BlankLineAfterLastUse', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($end + 1); $i < $closer; $i++) {
                        if ($tokens[$i]['line'] === $tokens[$end]['line']) {
                            continue;
                        }

                        if ($tokens[$i]['line'] === $tokens[$closer]['line']) {
                            // Don't remove indents.
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }//end if
        } else if ($tokens[$next]['code'] !== T_USE) {
            // Comments are allowed on the same line as the use statement, so make sure
            // we don't error for those.
            for ($next = ($end + 1); $next < $tokens[$ooToken]['scope_closer']; $next++) {
                if ($tokens[$next]['code'] === T_WHITESPACE) {
                    continue;
                }

                if (isset(Tokens::$commentTokens[$tokens[$next]['code']]) === true
                    && $tokens[$next]['line'] === $tokens[$end]['line']
                ) {
                    continue;
                }

                break;
            }

            if ($tokens[$next]['line'] <= ($tokens[$end]['line'] + 1)) {
                $error = 'There must be a blank line following the last trait import statement';
                $fix   = $phpcsFile->addFixableError($error, $end, 'NoBlankLineAfterUse');
                if ($fix === true) {
                    if ($tokens[$next]['line'] === $tokens[$stackPtr]['line']) {
                        $phpcsFile->fixer->addContentBefore($next, $phpcsFile->eolChar.$phpcsFile->eolChar);
                    } else {
                        for ($i = ($next - 1); $i > $end; $i--) {
                            if ($tokens[$i]['line'] !== $tokens[$next]['line']) {
                                break;
                            }
                        }

                        $phpcsFile->fixer->addNewlineBefore(($i + 1));
                    }
                }
            }
        }//end if

    }//end process()


    /**
     * Processes a group use statement.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    protected function processUseGroup(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $opener = $tokens[$stackPtr]['scope_opener'];
        $closer = $tokens[$stackPtr]['scope_closer'];

        if ($tokens[$opener]['line'] !== $tokens[$stackPtr]['line']) {
            $error = 'The opening brace of a trait import statement must be on the same line as the USE keyword';
            // Figure out if we can fix this error.
            $canFix = true;
            for ($i = ($stackPtr + 1); $i < $opener; $i++) {
                if ($tokens[$i]['line'] !== $tokens[($i + 1)]['line']
                    && $tokens[$i]['code'] !== T_WHITESPACE
                ) {
                    $canFix = false;
                    break;
                }
            }

            if ($canFix === true) {
                $fix = $phpcsFile->addFixableError($error, $opener, 'OpenBraceNewLine');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($stackPtr + 1); $i < $opener; $i++) {
                        if ($tokens[$i]['line'] !== $tokens[($i + 1)]['line']) {
                            // Everything should have a single space around it.
                            $phpcsFile->fixer->replaceToken($i, ' ');
                        }
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            } else {
                $phpcsFile->addError($error, $opener, 'OpenBraceNewLine');
            }
        }//end if

        $error = 'Expected 1 space before opening brace in trait import statement; %s found';
        if ($tokens[($opener - 1)]['code'] !== T_WHITESPACE) {
            $data = ['0'];
            $fix  = $phpcsFile->addFixableError($error, $opener, 'SpaceBeforeOpeningBrace', $data);
            if ($fix === true) {
                $phpcsFile->fixer->addContentBefore($opener, ' ');
            }
        } else if ($tokens[($opener - 1)]['content'] !== ' ') {
            $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($opener - 1), null, true);
            if ($tokens[$prev]['line'] !== $tokens[$opener]['line']) {
                $found = 'newline';
            } else {
                $found = $tokens[($opener - 1)]['length'];
            }

            $data = [$found];
            $fix  = $phpcsFile->addFixableError($error, $opener, 'SpaceBeforeOpeningBrace', $data);
            if ($fix === true) {
                if ($found === 'newline') {
                    $phpcsFile->fixer->beginChangeset();
                    for ($x = ($opener - 1); $x > $prev; $x--) {
                        $phpcsFile->fixer->replaceToken($x, '');
                    }

                    $phpcsFile->fixer->addContentBefore($opener, ' ');
                    $phpcsFile->fixer->endChangeset();
                } else {
                    $phpcsFile->fixer->replaceToken(($opener - 1), ' ');
                }
            }
        }//end if

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($opener + 1), ($closer - 1), true);
        if ($next !== false && $tokens[$next]['line'] !== ($tokens[$opener]['line'] + 1)) {
            $error     = 'First trait conflict resolution statement must be on the line after the opening brace';
            $nextNonWs = $phpcsFile->findNext(T_WHITESPACE, ($opener + 1), ($closer - 1), true);
            if ($nextNonWs !== $next) {
                $phpcsFile->addError($error, $opener, 'SpaceAfterOpeningBrace');
            } else {
                $fix = $phpcsFile->addFixableError($error, $opener, 'SpaceAfterOpeningBrace');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($x = ($opener + 1); $x < $next; $x++) {
                        if ($tokens[$x]['line'] === $tokens[$next]['line']) {
                            // Preserve indent.
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($x, '');
                    }

                    $phpcsFile->fixer->addNewline($opener);
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }//end if

        for ($i = ($stackPtr + 1); $i < $opener; $i++) {
            if ($tokens[$i]['code'] !== T_COMMA) {
                continue;
            }

            if ($tokens[($i - 1)]['code'] === T_WHITESPACE) {
                $error = 'Expected no space before comma in trait import statement; %s found';
                $data  = [$tokens[($i - 1)]['length']];
                $fix   = $phpcsFile->addFixableError($error, $i, 'SpaceBeforeComma', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($i - 1), '');
                }
            }

            $error = 'Expected 1 space after comma in trait import statement; %s found';
            if ($tokens[($i + 1)]['code'] !== T_WHITESPACE) {
                $data = ['0'];
                $fix  = $phpcsFile->addFixableError($error, $i, 'SpaceAfterComma', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->addContent($i, ' ');
                }
            } else if ($tokens[($i + 1)]['content'] !== ' ') {
                $next = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), $opener, true);
                if ($tokens[$next]['line'] !== $tokens[$i]['line']) {
                    $found = 'newline';
                } else {
                    $found = $tokens[($i + 1)]['length'];
                }

                $data = [$found];
                $fix  = $phpcsFile->addFixableError($error, $i, 'SpaceAfterComma', $data);
                if ($fix === true) {
                    if ($found === 'newline') {
                        $phpcsFile->fixer->beginChangeset();
                        for ($x = ($i + 1); $x < $next; $x++) {
                            $phpcsFile->fixer->replaceToken($x, '');
                        }

                        $phpcsFile->fixer->addContent($i, ' ');
                        $phpcsFile->fixer->endChangeset();
                    } else {
                        $phpcsFile->fixer->replaceToken(($i + 1), ' ');
                    }
                }
            }//end if
        }//end for

        for ($i = ($opener + 1); $i < $closer; $i++) {
            if ($tokens[$i]['code'] === T_INSTEADOF) {
                $error = 'Expected 1 space before INSTEADOF in trait import statement; %s found';
                if ($tokens[($i - 1)]['code'] !== T_WHITESPACE) {
                    $data = ['0'];
                    $fix  = $phpcsFile->addFixableError($error, $i, 'SpaceBeforeInsteadof', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->addContentBefore($i, ' ');
                    }
                } else if ($tokens[($i - 1)]['content'] !== ' ') {
                    $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($i - 1), $opener, true);
                    if ($tokens[$prev]['line'] !== $tokens[$i]['line']) {
                        $found = 'newline';
                    } else {
                        $found = $tokens[($i - 1)]['length'];
                    }

                    $data = [$found];

                    $prevNonWs = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($i - 1), $opener, true);
                    if ($prevNonWs !== $prev) {
                        $phpcsFile->addError($error, $i, 'SpaceBeforeInsteadof', $data);
                    } else {
                        $fix = $phpcsFile->addFixableError($error, $i, 'SpaceBeforeInsteadof', $data);
                        if ($fix === true) {
                            if ($found === 'newline') {
                                $phpcsFile->fixer->beginChangeset();
                                for ($x = ($i - 1); $x > $prev; $x--) {
                                    $phpcsFile->fixer->replaceToken($x, '');
                                }

                                $phpcsFile->fixer->addContentBefore($i, ' ');
                                $phpcsFile->fixer->endChangeset();
                            } else {
                                $phpcsFile->fixer->replaceToken(($i - 1), ' ');
                            }
                        }
                    }
                }//end if

                $error = 'Expected 1 space after INSTEADOF in trait import statement; %s found';
                if ($tokens[($i + 1)]['code'] !== T_WHITESPACE) {
                    $data = ['0'];
                    $fix  = $phpcsFile->addFixableError($error, $i, 'SpaceAfterInsteadof', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->addContent($i, ' ');
                    }
                } else if ($tokens[($i + 1)]['content'] !== ' ') {
                    $next = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), $closer, true);
                    if ($tokens[$next]['line'] !== $tokens[$i]['line']) {
                        $found = 'newline';
                    } else {
                        $found = $tokens[($i + 1)]['length'];
                    }

                    $data = [$found];

                    $nextNonWs = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), $closer, true);
                    if ($nextNonWs !== $next) {
                        $phpcsFile->addError($error, $i, 'SpaceAfterInsteadof', $data);
                    } else {
                        $fix = $phpcsFile->addFixableError($error, $i, 'SpaceAfterInsteadof', $data);
                        if ($fix === true) {
                            if ($found === 'newline') {
                                $phpcsFile->fixer->beginChangeset();
                                for ($x = ($i + 1); $x < $next; $x++) {
                                    $phpcsFile->fixer->replaceToken($x, '');
                                }

                                $phpcsFile->fixer->addContent($i, ' ');
                                $phpcsFile->fixer->endChangeset();
                            } else {
                                $phpcsFile->fixer->replaceToken(($i + 1), ' ');
                            }
                        }
                    }
                }//end if
            }//end if

            if ($tokens[$i]['code'] === T_AS) {
                $error = 'Expected 1 space before AS in trait import statement; %s found';
                if ($tokens[($i - 1)]['code'] !== T_WHITESPACE) {
                    $data = ['0'];
                    $fix  = $phpcsFile->addFixableError($error, $i, 'SpaceBeforeAs', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->addContentBefore($i, ' ');
                    }
                } else if ($tokens[($i - 1)]['content'] !== ' ') {
                    $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($i - 1), $opener, true);
                    if ($tokens[$prev]['line'] !== $tokens[$i]['line']) {
                        $found = 'newline';
                    } else {
                        $found = $tokens[($i - 1)]['length'];
                    }

                    $data = [$found];

                    $prevNonWs = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($i - 1), $opener, true);
                    if ($prevNonWs !== $prev) {
                        $phpcsFile->addError($error, $i, 'SpaceBeforeAs', $data);
                    } else {
                        $fix = $phpcsFile->addFixableError($error, $i, 'SpaceBeforeAs', $data);
                        if ($fix === true) {
                            if ($found === 'newline') {
                                $phpcsFile->fixer->beginChangeset();
                                for ($x = ($i - 1); $x > $prev; $x--) {
                                    $phpcsFile->fixer->replaceToken($x, '');
                                }

                                $phpcsFile->fixer->addContentBefore($i, ' ');
                                $phpcsFile->fixer->endChangeset();
                            } else {
                                $phpcsFile->fixer->replaceToken(($i - 1), ' ');
                            }
                        }
                    }
                }//end if

                $error = 'Expected 1 space after AS in trait import statement; %s found';
                if ($tokens[($i + 1)]['code'] !== T_WHITESPACE) {
                    $data = ['0'];
                    $fix  = $phpcsFile->addFixableError($error, $i, 'SpaceAfterAs', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->addContent($i, ' ');
                    }
                } else if ($tokens[($i + 1)]['content'] !== ' ') {
                    $next = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), $closer, true);
                    if ($tokens[$next]['line'] !== $tokens[$i]['line']) {
                        $found = 'newline';
                    } else {
                        $found = $tokens[($i + 1)]['length'];
                    }

                    $data = [$found];

                    $nextNonWs = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), $closer, true);
                    if ($nextNonWs !== $next) {
                        $phpcsFile->addError($error, $i, 'SpaceAfterAs', $data);
                    } else {
                        $fix = $phpcsFile->addFixableError($error, $i, 'SpaceAfterAs', $data);
                        if ($fix === true) {
                            if ($found === 'newline') {
                                $phpcsFile->fixer->beginChangeset();
                                for ($x = ($i + 1); $x < $next; $x++) {
                                    $phpcsFile->fixer->replaceToken($x, '');
                                }

                                $phpcsFile->fixer->addContent($i, ' ');
                                $phpcsFile->fixer->endChangeset();
                            } else {
                                $phpcsFile->fixer->replaceToken(($i + 1), ' ');
                            }
                        }
                    }
                }//end if
            }//end if

            if ($tokens[$i]['code'] === T_SEMICOLON) {
                if ($tokens[($i - 1)]['code'] === T_WHITESPACE) {
                    $error = 'Expected no space before semicolon in trait import statement; %s found';
                    $data  = [$tokens[($i - 1)]['length']];
                    $fix   = $phpcsFile->addFixableError($error, $i, 'SpaceBeforeSemicolon', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($i - 1), '');
                    }
                }

                $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), ($closer - 1), true);
                if ($next !== false && $tokens[$next]['line'] === $tokens[$i]['line']) {
                    $error     = 'Each trait conflict resolution statement must be on a line by itself';
                    $nextNonWs = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), ($closer - 1), true);
                    if ($nextNonWs !== $next) {
                        $phpcsFile->addError($error, $i, 'ConflictSameLine');
                    } else {
                        $fix = $phpcsFile->addFixableError($error, $i, 'ConflictSameLine');
                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();
                            if ($tokens[($i + 1)]['code'] === T_WHITESPACE) {
                                $phpcsFile->fixer->replaceToken(($i + 1), '');
                            }

                            $phpcsFile->fixer->addNewline($i);
                            $phpcsFile->fixer->endChangeset();
                        }
                    }
                }
            }//end if
        }//end for

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($closer - 1), ($opener + 1), true);
        if ($prev !== false && $tokens[$prev]['line'] !== ($tokens[$closer]['line'] - 1)) {
            $error     = 'Closing brace must be on the line after the last trait conflict resolution statement';
            $prevNonWs = $phpcsFile->findPrevious(T_WHITESPACE, ($closer - 1), ($opener + 1), true);
            if ($prevNonWs !== $prev) {
                $phpcsFile->addError($error, $closer, 'SpaceBeforeClosingBrace');
            } else {
                $fix = $phpcsFile->addFixableError($error, $closer, 'SpaceBeforeClosingBrace');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($x = ($closer - 1); $x > $prev; $x--) {
                        if ($tokens[$x]['line'] === $tokens[$closer]['line']) {
                            // Preserve indent.
                            continue;
                        }

                        $phpcsFile->fixer->replaceToken($x, '');
                    }

                    $phpcsFile->fixer->addNewline($prev);
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }//end if

    }//end processUseGroup()


    /**
     * Processes a single use statement.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    protected function processUseStatement(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $error = 'Expected 1 space after USE in trait import statement; %s found';
        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            $data = ['0'];
            $fix  = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterAs', $data);
            if ($fix === true) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            }
        } else if ($tokens[($stackPtr + 1)]['content'] !== ' ') {
            $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            if ($tokens[$next]['line'] !== $tokens[$stackPtr]['line']) {
                $found = 'newline';
            } else {
                $found = $tokens[($stackPtr + 1)]['length'];
            }

            $data = [$found];
            $fix  = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterAs', $data);
            if ($fix === true) {
                if ($found === 'newline') {
                    $phpcsFile->fixer->beginChangeset();
                    for ($x = ($stackPtr + 1); $x < $next; $x++) {
                        $phpcsFile->fixer->replaceToken($x, '');
                    }

                    $phpcsFile->fixer->addContent($stackPtr, ' ');
                    $phpcsFile->fixer->endChangeset();
                } else {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                }
            }
        }//end if

        $next = $phpcsFile->findNext([T_COMMA, T_SEMICOLON], ($stackPtr + 1));
        if ($next !== false && $tokens[$next]['code'] === T_COMMA) {
            $error = 'Each imported trait must have its own "use" import statement';
            $fix   = $phpcsFile->addFixableError($error, $next, 'MultipleImport');
            if ($fix === true) {
                $padding = str_repeat(' ', ($tokens[$stackPtr]['column'] - 1));
                $phpcsFile->fixer->replaceToken($next, ';'.$phpcsFile->eolChar.$padding.'use ');
            }
        }

    }//end processUseStatement()


}//end class
