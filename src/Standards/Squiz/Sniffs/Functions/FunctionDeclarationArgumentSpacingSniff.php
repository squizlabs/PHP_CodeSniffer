<?php
/**
 * Checks that arguments in function declarations are spaced correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class FunctionDeclarationArgumentSpacingSniff implements Sniff
{

    /**
     * How many spaces should surround the equals signs.
     *
     * @var integer
     */
    public $equalsSpacing = 0;

    /**
     * How many spaces should follow the opening bracket.
     *
     * @var integer
     */
    public $requiredSpacesAfterOpen = 0;

    /**
     * How many spaces should precede the closing bracket.
     *
     * @var integer
     */
    public $requiredSpacesBeforeClose = 0;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_FUNCTION,
            T_CLOSURE,
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

        if (isset($tokens[$stackPtr]['parenthesis_opener']) === false
            || isset($tokens[$stackPtr]['parenthesis_closer']) === false
            || $tokens[$stackPtr]['parenthesis_opener'] === null
            || $tokens[$stackPtr]['parenthesis_closer'] === null
        ) {
            return;
        }

        $this->equalsSpacing           = (int) $this->equalsSpacing;
        $this->requiredSpacesAfterOpen = (int) $this->requiredSpacesAfterOpen;
        $this->requiredSpacesBeforeClose = (int) $this->requiredSpacesBeforeClose;

        $openBracket = $tokens[$stackPtr]['parenthesis_opener'];
        $this->processBracket($phpcsFile, $openBracket);

        if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
            $use = $phpcsFile->findNext(T_USE, ($tokens[$openBracket]['parenthesis_closer'] + 1), $tokens[$stackPtr]['scope_opener']);
            if ($use !== false) {
                $openBracket = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($use + 1), null);
                $this->processBracket($phpcsFile, $openBracket);
            }
        }

    }//end process()


    /**
     * Processes the contents of a single set of brackets.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file being scanned.
     * @param int                         $openBracket The position of the open bracket
     *                                                 in the stack passed in $tokens.
     *
     * @return void
     */
    public function processBracket($phpcsFile, $openBracket)
    {
        $tokens       = $phpcsFile->getTokens();
        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
        $multiLine    = ($tokens[$openBracket]['line'] !== $tokens[$closeBracket]['line']);

        $nextParam = $openBracket;
        $params    = [];
        while (($nextParam = $phpcsFile->findNext(T_VARIABLE, ($nextParam + 1), $closeBracket)) !== false) {
            $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($nextParam + 1), ($closeBracket + 1), true);
            if ($nextToken === false) {
                break;
            }

            $nextCode = $tokens[$nextToken]['code'];

            if ($nextCode === T_EQUAL) {
                // Check parameter default spacing.
                $spacesBefore = 0;
                if (($nextToken - $nextParam) > 1) {
                    $spacesBefore = strlen($tokens[($nextParam + 1)]['content']);
                }

                if ($spacesBefore !== $this->equalsSpacing) {
                    $error = 'Incorrect spacing between argument "%s" and equals sign; expected '.$this->equalsSpacing.' but found %s';
                    $data  = [
                        $tokens[$nextParam]['content'],
                        $spacesBefore,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $nextToken, 'SpaceBeforeEquals', $data);
                    if ($fix === true) {
                        $padding = str_repeat(' ', $this->equalsSpacing);
                        if ($spacesBefore === 0) {
                            $phpcsFile->fixer->addContentBefore($nextToken, $padding);
                        } else {
                            $phpcsFile->fixer->replaceToken(($nextToken - 1), $padding);
                        }
                    }
                }//end if

                $spacesAfter = 0;
                if ($tokens[($nextToken + 1)]['code'] === T_WHITESPACE) {
                    $spacesAfter = strlen($tokens[($nextToken + 1)]['content']);
                }

                if ($spacesAfter !== $this->equalsSpacing) {
                    $error = 'Incorrect spacing between default value and equals sign for argument "%s"; expected '.$this->equalsSpacing.' but found %s';
                    $data  = [
                        $tokens[$nextParam]['content'],
                        $spacesAfter,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $nextToken, 'SpaceAfterDefault', $data);
                    if ($fix === true) {
                        $padding = str_repeat(' ', $this->equalsSpacing);
                        if ($spacesAfter === 0) {
                            $phpcsFile->fixer->addContent($nextToken, $padding);
                        } else {
                            $phpcsFile->fixer->replaceToken(($nextToken + 1), $padding);
                        }
                    }
                }//end if
            }//end if

            // Find and check the comma (if there is one).
            $nextComma = $phpcsFile->findNext(T_COMMA, ($nextParam + 1), $closeBracket);
            if ($nextComma !== false) {
                // Comma found.
                if ($tokens[($nextComma - 1)]['code'] === T_WHITESPACE) {
                    $error = 'Expected 0 spaces between argument "%s" and comma; %s found';
                    $data  = [
                        $tokens[$nextParam]['content'],
                        strlen($tokens[($nextComma - 1)]['content']),
                    ];

                    $fix = $phpcsFile->addFixableError($error, $nextToken, 'SpaceBeforeComma', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($nextComma - 1), '');
                    }
                }
            }

            $checkToken = ($nextParam - 1);
            $prev       = $phpcsFile->findPrevious(T_WHITESPACE, $checkToken, null, true);
            if ($tokens[$prev]['code'] === T_ELLIPSIS) {
                $checkToken = ($prev - 1);
            }

            // Take references into account when expecting the
            // location of whitespace.
            if ($phpcsFile->isReference($checkToken) === true) {
                $whitespace = ($checkToken - 1);
            } else {
                $whitespace = $checkToken;
            }

            if (empty($params) === false) {
                // This is not the first argument in the function declaration.
                $arg = $tokens[$nextParam]['content'];

                // Before we throw an error, make sure there is no type hint.
                $comma     = $phpcsFile->findPrevious(T_COMMA, ($nextParam - 1));
                $nextToken = $phpcsFile->findNext(Tokens::$emptyTokens, ($comma + 1), null, true);
                if ($phpcsFile->isReference($nextToken) === true) {
                    $nextToken++;
                }

                $gap = 0;
                if ($tokens[$whitespace]['code'] === T_WHITESPACE) {
                    $gap = strlen($tokens[$whitespace]['content']);
                }

                if ($nextToken !== $nextParam) {
                    // There was a type hint, so check the spacing between
                    // the hint and the variable as well.
                    $hint = $tokens[$nextToken]['content'];

                    if ($gap !== 1) {
                        $error = 'Expected 1 space between type hint and argument "%s"; %s found';
                        $data  = [
                            $arg,
                            $gap,
                        ];
                        $fix   = $phpcsFile->addFixableError($error, $nextToken, 'SpacingAfterHint', $data);
                        if ($fix === true) {
                            if ($gap === 0) {
                                $phpcsFile->fixer->addContent($whitespace, ' ');
                            } else {
                                $phpcsFile->fixer->replaceToken($whitespace, ' ');
                            }
                        }
                    }

                    if ($multiLine === false) {
                        if ($tokens[($comma + 1)]['code'] !== T_WHITESPACE) {
                            $error = 'Expected 1 space between comma and type hint "%s"; 0 found';
                            $data  = [$hint];
                            $fix   = $phpcsFile->addFixableError($error, $nextToken, 'NoSpaceBeforeHint', $data);
                            if ($fix === true) {
                                $phpcsFile->fixer->addContent($comma, ' ');
                            }
                        } else {
                            $gap = strlen($tokens[($comma + 1)]['content']);
                            if ($gap !== 1) {
                                $error = 'Expected 1 space between comma and type hint "%s"; %s found';
                                $data  = [
                                    $hint,
                                    $gap,
                                ];
                                $fix   = $phpcsFile->addFixableError($error, $nextToken, 'SpacingBeforeHint', $data);
                                if ($fix === true) {
                                    $phpcsFile->fixer->replaceToken(($comma + 1), ' ');
                                }
                            }
                        }//end if
                    }//end if
                } else {
                    // No type hint.
                    if ($gap === 0) {
                        $error = 'Expected 1 space between comma and argument "%s"; 0 found';
                        $data  = [$arg];
                        $fix   = $phpcsFile->addFixableError($error, $nextToken, 'NoSpaceBeforeArg', $data);
                        if ($fix === true) {
                            $phpcsFile->fixer->addContent($whitespace, ' ');
                        }
                    } else if ($gap !== 1) {
                        // Just make sure this is not actually an indent.
                        if ($tokens[$whitespace]['line'] === $tokens[($whitespace - 1)]['line']) {
                            $error = 'Expected 1 space between comma and argument "%s"; %s found';
                            $data  = [
                                $arg,
                                $gap,
                            ];

                            $fix = $phpcsFile->addFixableError($error, $nextToken, 'SpacingBeforeArg', $data);
                            if ($fix === true) {
                                $phpcsFile->fixer->replaceToken($whitespace, ' ');
                            }
                        }
                    }//end if
                }//end if
            } else {
                $gap = 0;
                if ($tokens[$whitespace]['code'] === T_WHITESPACE) {
                    $gap = strlen($tokens[$whitespace]['content']);
                }

                $arg = $tokens[$nextParam]['content'];

                // Before we throw an error, make sure there is no type hint.
                $bracket   = $phpcsFile->findPrevious(T_OPEN_PARENTHESIS, ($nextParam - 1));
                $nextToken = $phpcsFile->findNext(Tokens::$emptyTokens, ($bracket + 1), null, true);
                if ($phpcsFile->isReference($nextToken) === true) {
                    $nextToken++;
                }

                if ($gap !== 1
                    && $tokens[$nextToken]['code'] !== T_ELLIPSIS
                    && $nextToken !== $nextParam
                ) {
                    $error = 'Expected 1 space between type hint and argument "%s"; %s found';
                    $data  = [
                        $arg,
                        $gap,
                    ];
                    $fix   = $phpcsFile->addFixableError($error, $nextToken, 'SpacingAfterHint', $data);
                    if ($fix === true) {
                        if ($gap === 0) {
                            $phpcsFile->fixer->addContent($nextToken, ' ');
                        } else {
                            $phpcsFile->fixer->replaceToken(($nextToken + 1), ' ');
                        }
                    }
                }
            }//end if

            $params[] = $nextParam;
        }//end while

        if (empty($params) === true) {
            // Check spacing around parenthesis.
            $next = $phpcsFile->findNext(T_WHITESPACE, ($openBracket + 1), $closeBracket, true);
            if ($next === false) {
                if (($closeBracket - $openBracket) !== 1) {
                    $error = 'Expected 0 spaces between parenthesis of function declaration; %s found';
                    $data  = [$tokens[($openBracket + 1)]['length']];
                    $fix   = $phpcsFile->addFixableError($error, $openBracket, 'SpacingBetween', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($openBracket + 1), '');
                    }
                }

                // No params, so we don't check normal spacing rules.
                return;
            }
        }

        // Only check spacing around parenthesis for single line definitions.
        if ($multiLine === true) {
            return;
        }

        $gap = 0;
        if ($tokens[($closeBracket - 1)]['code'] === T_WHITESPACE) {
            $gap = $tokens[($closeBracket - 1)]['length'];
        }

        if ($gap !== $this->requiredSpacesBeforeClose) {
            $error = 'Expected %s spaces before closing parenthesis; %s found';
            $data  = [
                $this->requiredSpacesBeforeClose,
                $gap,
            ];
            $fix   = $phpcsFile->addFixableError($error, $closeBracket, 'SpacingBeforeClose', $data);
            if ($fix === true) {
                $padding = str_repeat(' ', $this->requiredSpacesBeforeClose);
                if ($gap === 0) {
                    $phpcsFile->fixer->addContentBefore($closeBracket, $padding);
                } else {
                    $phpcsFile->fixer->replaceToken(($closeBracket - 1), $padding);
                }
            }
        }

        $gap = 0;
        if ($tokens[($openBracket + 1)]['code'] === T_WHITESPACE) {
            $gap = $tokens[($openBracket + 1)]['length'];
        }

        if ($gap !== $this->requiredSpacesAfterOpen) {
            $error = 'Expected %s spaces after opening parenthesis; %s found';
            $data  = [
                $this->requiredSpacesAfterOpen,
                $gap,
            ];
            $fix   = $phpcsFile->addFixableError($error, $openBracket, 'SpacingAfterOpen', $data);
            if ($fix === true) {
                $padding = str_repeat(' ', $this->requiredSpacesAfterOpen);
                if ($gap === 0) {
                    $phpcsFile->fixer->addContent($openBracket, $padding);
                } else {
                    $phpcsFile->fixer->replaceToken(($openBracket + 1), $padding);
                }
            }
        }

    }//end processBracket()


}//end class
