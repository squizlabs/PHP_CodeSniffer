<?php
/**
 * Verifies that there is a space between each condition of for loops.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ForLoopDeclarationSniff implements Sniff
{

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
     * Allow newlines instead of spaces.
     *
     * @var boolean
     */
    public $ignoreNewlines = false;

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
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_FOR];

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
        $this->requiredSpacesAfterOpen   = (int) $this->requiredSpacesAfterOpen;
        $this->requiredSpacesBeforeClose = (int) $this->requiredSpacesBeforeClose;
        $tokens = $phpcsFile->getTokens();

        $openingBracket = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        if ($openingBracket === false) {
            $error = 'Possible parse error: no opening parenthesis for FOR keyword';
            $phpcsFile->addWarning($error, $stackPtr, 'NoOpenBracket');
            return;
        }

        $closingBracket = $tokens[$openingBracket]['parenthesis_closer'];

        if ($this->requiredSpacesAfterOpen === 0
            && $tokens[($openingBracket + 1)]['code'] === T_WHITESPACE
        ) {
            $nextNonWhiteSpace = $phpcsFile->findNext(T_WHITESPACE, ($openingBracket + 1), $closingBracket, true);
            if ($this->ignoreNewlines === false
                || $tokens[$nextNonWhiteSpace]['line'] === $tokens[$openingBracket]['line']
            ) {
                $error = 'Whitespace found after opening bracket of FOR loop';
                $fix   = $phpcsFile->addFixableError($error, $openingBracket, 'SpacingAfterOpen');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($openingBracket + 1); $i < $closingBracket; $i++) {
                        if ($tokens[$i]['code'] !== T_WHITESPACE) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }
        } else if ($this->requiredSpacesAfterOpen > 0) {
            $nextNonWhiteSpace = $phpcsFile->findNext(T_WHITESPACE, ($openingBracket + 1), $closingBracket, true);
            $spaceAfterOpen    = 0;
            if ($tokens[$openingBracket]['line'] !== $tokens[$nextNonWhiteSpace]['line']) {
                $spaceAfterOpen = 'newline';
            } else if ($tokens[($openingBracket + 1)]['code'] === T_WHITESPACE) {
                $spaceAfterOpen = $tokens[($openingBracket + 1)]['length'];
            }

            if ($spaceAfterOpen !== $this->requiredSpacesAfterOpen
                && ($this->ignoreNewlines === false
                || $spaceAfterOpen !== 'newline')
            ) {
                $error = 'Expected %s spaces after opening bracket; %s found';
                $data  = [
                    $this->requiredSpacesAfterOpen,
                    $spaceAfterOpen,
                ];
                $fix   = $phpcsFile->addFixableError($error, $openingBracket, 'SpacingAfterOpen', $data);
                if ($fix === true) {
                    $padding = str_repeat(' ', $this->requiredSpacesAfterOpen);
                    if ($spaceAfterOpen === 0) {
                        $phpcsFile->fixer->addContent($openingBracket, $padding);
                    } else {
                        $phpcsFile->fixer->beginChangeset();
                        $phpcsFile->fixer->replaceToken(($openingBracket + 1), $padding);
                        for ($i = ($openingBracket + 2); $i < $nextNonWhiteSpace; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }
            }//end if
        }//end if

        $prevNonWhiteSpace  = $phpcsFile->findPrevious(T_WHITESPACE, ($closingBracket - 1), $openingBracket, true);
        $beforeClosefixable = true;
        if ($tokens[$prevNonWhiteSpace]['line'] !== $tokens[$closingBracket]['line']
            && isset(Tokens::$emptyTokens[$tokens[$prevNonWhiteSpace]['code']]) === true
        ) {
            $beforeClosefixable = false;
        }

        if ($this->requiredSpacesBeforeClose === 0
            && $tokens[($closingBracket - 1)]['code'] === T_WHITESPACE
            && ($this->ignoreNewlines === false
            || $tokens[$prevNonWhiteSpace]['line'] === $tokens[$closingBracket]['line'])
        ) {
            $error = 'Whitespace found before closing bracket of FOR loop';

            if ($beforeClosefixable === false) {
                $phpcsFile->addError($error, $closingBracket, 'SpacingBeforeClose');
            } else {
                $fix = $phpcsFile->addFixableError($error, $closingBracket, 'SpacingBeforeClose');
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
        } else if ($this->requiredSpacesBeforeClose > 0) {
            $spaceBeforeClose = 0;
            if ($tokens[$closingBracket]['line'] !== $tokens[$prevNonWhiteSpace]['line']) {
                $spaceBeforeClose = 'newline';
            } else if ($tokens[($closingBracket - 1)]['code'] === T_WHITESPACE) {
                $spaceBeforeClose = $tokens[($closingBracket - 1)]['length'];
            }

            if ($this->requiredSpacesBeforeClose !== $spaceBeforeClose
                && ($this->ignoreNewlines === false
                || $spaceBeforeClose !== 'newline')
            ) {
                $error = 'Expected %s spaces before closing bracket; %s found';
                $data  = [
                    $this->requiredSpacesBeforeClose,
                    $spaceBeforeClose,
                ];

                if ($beforeClosefixable === false) {
                    $phpcsFile->addError($error, $closingBracket, 'SpacingBeforeClose', $data);
                } else {
                    $fix = $phpcsFile->addFixableError($error, $closingBracket, 'SpacingBeforeClose', $data);
                    if ($fix === true) {
                        $padding = str_repeat(' ', $this->requiredSpacesBeforeClose);
                        if ($spaceBeforeClose === 0) {
                            $phpcsFile->fixer->addContentBefore($closingBracket, $padding);
                        } else {
                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->replaceToken(($closingBracket - 1), $padding);
                            for ($i = ($closingBracket - 2); $i > $prevNonWhiteSpace; $i--) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            $phpcsFile->fixer->endChangeset();
                        }
                    }
                }
            }//end if
        }//end if

        /*
         * Check whitespace around each of the semicolon tokens.
         */

        $semicolonCount     = 0;
        $semicolon          = $openingBracket;
        $targetNestinglevel = 0;
        if (isset($tokens[$openingBracket]['conditions']) === true) {
            $targetNestinglevel = count($tokens[$openingBracket]['conditions']);
        }

        do {
            $semicolon = $phpcsFile->findNext(T_SEMICOLON, ($semicolon + 1), $closingBracket);
            if ($semicolon === false) {
                break;
            }

            if (isset($tokens[$semicolon]['conditions']) === true
                && count($tokens[$semicolon]['conditions']) > $targetNestinglevel
            ) {
                // Semicolon doesn't belong to the for().
                continue;
            }

            ++$semicolonCount;

            $humanReadableCount = 'first';
            if ($semicolonCount !== 1) {
                $humanReadableCount = 'second';
            }

            $humanReadableCode = ucfirst($humanReadableCount);
            $data = [$humanReadableCount];

            // Only examine the space before the first semicolon if the first expression is not empty.
            // If it *is* empty, leave it up to the `SpacingAfterOpen` logic.
            $prevNonWhiteSpace = $phpcsFile->findPrevious(T_WHITESPACE, ($semicolon - 1), $openingBracket, true);
            if ($semicolonCount !== 1 || $prevNonWhiteSpace !== $openingBracket) {
                if ($tokens[($semicolon - 1)]['code'] === T_WHITESPACE) {
                    $error     = 'Whitespace found before %s semicolon of FOR loop';
                    $errorCode = 'SpacingBefore'.$humanReadableCode;
                    $fix       = $phpcsFile->addFixableError($error, $semicolon, $errorCode, $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($semicolon - 1); $i > $prevNonWhiteSpace; $i--) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }
            }

            // Only examine the space after the second semicolon if the last expression is not empty.
            // If it *is* empty, leave it up to the `SpacingBeforeClose` logic.
            $nextNonWhiteSpace = $phpcsFile->findNext(T_WHITESPACE, ($semicolon + 1), ($closingBracket + 1), true);
            if ($semicolonCount !== 2 || $nextNonWhiteSpace !== $closingBracket) {
                if ($tokens[($semicolon + 1)]['code'] !== T_WHITESPACE
                    && $tokens[($semicolon + 1)]['code'] !== T_SEMICOLON
                ) {
                    $error     = 'Expected 1 space after %s semicolon of FOR loop; 0 found';
                    $errorCode = 'NoSpaceAfter'.$humanReadableCode;
                    $fix       = $phpcsFile->addFixableError($error, $semicolon, $errorCode, $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->addContent($semicolon, ' ');
                    }
                } else if ($tokens[($semicolon + 1)]['code'] === T_WHITESPACE
                    && $tokens[$nextNonWhiteSpace]['code'] !== T_SEMICOLON
                ) {
                    $spaces = $tokens[($semicolon + 1)]['length'];
                    if ($tokens[$semicolon]['line'] !== $tokens[$nextNonWhiteSpace]['line']) {
                        $spaces = 'newline';
                    }

                    if ($spaces !== 1
                        && ($this->ignoreNewlines === false
                        || $spaces !== 'newline')
                    ) {
                        $error     = 'Expected 1 space after %s semicolon of FOR loop; %s found';
                        $errorCode = 'SpacingAfter'.$humanReadableCode;
                        $data[]    = $spaces;
                        $fix       = $phpcsFile->addFixableError($error, $semicolon, $errorCode, $data);
                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->replaceToken(($semicolon + 1), ' ');
                            for ($i = ($semicolon + 2); $i < $nextNonWhiteSpace; $i++) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            $phpcsFile->fixer->endChangeset();
                        }
                    }
                }//end if
            }//end if
        } while ($semicolonCount < 2);

    }//end process()


}//end class
