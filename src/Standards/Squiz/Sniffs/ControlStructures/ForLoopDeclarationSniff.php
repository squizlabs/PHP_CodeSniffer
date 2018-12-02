<?php
/**
 * Verifies that there is a space between each condition of for loops.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

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

        if ($this->requiredSpacesAfterOpen === 0 && $tokens[($openingBracket + 1)]['code'] === T_WHITESPACE) {
            $error = 'Whitespace found after opening bracket of FOR loop';
            $fix   = $phpcsFile->addFixableError($error, $openingBracket, 'SpacingAfterOpen');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken(($openingBracket + 1), '');
            }
        } else if ($this->requiredSpacesAfterOpen > 0) {
            $nextNonWhiteSpace = $phpcsFile->findNext(T_WHITESPACE, ($openingBracket + 1), $closingBracket, true);
            $spaceAfterOpen    = 0;
            if ($tokens[$openingBracket]['line'] !== $tokens[$nextNonWhiteSpace]['line']) {
                $spaceAfterOpen = 'newline';
            } else if ($tokens[($openingBracket + 1)]['code'] === T_WHITESPACE) {
                $spaceAfterOpen = $tokens[($openingBracket + 1)]['length'];
            }

            if ($spaceAfterOpen !== $this->requiredSpacesAfterOpen) {
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
                        $phpcsFile->fixer->replaceToken(($openingBracket + 1), $padding);
                    }
                }
            }
        }//end if

        if ($this->requiredSpacesBeforeClose === 0 && $tokens[($closingBracket - 1)]['code'] === T_WHITESPACE) {
            $error = 'Whitespace found before closing bracket of FOR loop';
            $fix   = $phpcsFile->addFixableError($error, $closingBracket, 'SpacingBeforeClose');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken(($closingBracket - 1), '');
            }
        } else if ($this->requiredSpacesBeforeClose > 0) {
            $prevNonWhiteSpace = $phpcsFile->findPrevious(T_WHITESPACE, ($closingBracket - 1), $openingBracket, true);
            $spaceBeforeClose  = 0;
            if ($tokens[$closingBracket]['line'] !== $tokens[$prevNonWhiteSpace]['line']) {
                $spaceBeforeClose = 'newline';
            } else if ($tokens[($closingBracket - 1)]['code'] === T_WHITESPACE) {
                $spaceBeforeClose = $tokens[($closingBracket - 1)]['length'];
            }

            if ($this->requiredSpacesBeforeClose !== $spaceBeforeClose) {
                $error = 'Expected %s spaces before closing bracket; %s found';
                $data  = [
                    $this->requiredSpacesBeforeClose,
                    $spaceBeforeClose,
                ];
                $fix   = $phpcsFile->addFixableError($error, $closingBracket, 'SpacingBeforeClose', $data);
                if ($fix === true) {
                    $padding = str_repeat(' ', $this->requiredSpacesBeforeClose);
                    if ($spaceBeforeClose === 0) {
                        $phpcsFile->fixer->addContentBefore($closingBracket, $padding);
                    } else {
                        $phpcsFile->fixer->replaceToken(($closingBracket - 1), $padding);
                    }
                }
            }
        }//end if

        $firstSemicolon = $phpcsFile->findNext(T_SEMICOLON, $openingBracket, $closingBracket);

        // Check whitespace around each of the tokens.
        if ($firstSemicolon !== false) {
            if ($tokens[($firstSemicolon - 1)]['code'] === T_WHITESPACE) {
                $error = 'Space found before first semicolon of FOR loop';
                $fix   = $phpcsFile->addFixableError($error, $firstSemicolon, 'SpacingBeforeFirst');
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($firstSemicolon - 1), '');
                }
            }

            if ($tokens[($firstSemicolon + 1)]['code'] !== T_WHITESPACE
                && $tokens[($firstSemicolon + 1)]['code'] !== T_SEMICOLON
            ) {
                $error = 'Expected 1 space after first semicolon of FOR loop; 0 found';
                $fix   = $phpcsFile->addFixableError($error, $firstSemicolon, 'NoSpaceAfterFirst');
                if ($fix === true) {
                    $phpcsFile->fixer->addContent($firstSemicolon, ' ');
                }
            } else {
                if (strlen($tokens[($firstSemicolon + 1)]['content']) !== 1) {
                    $spaces = strlen($tokens[($firstSemicolon + 1)]['content']);
                    $error  = 'Expected 1 space after first semicolon of FOR loop; %s found';
                    $data   = [$spaces];
                    $fix    = $phpcsFile->addFixableError($error, $firstSemicolon, 'SpacingAfterFirst', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($firstSemicolon + 1), ' ');
                    }
                }
            }

            $secondSemicolon = $phpcsFile->findNext(T_SEMICOLON, ($firstSemicolon + 1));

            if ($secondSemicolon !== false) {
                if ($tokens[($secondSemicolon - 1)]['code'] === T_WHITESPACE
                    && $tokens[($firstSemicolon + 1)]['code'] !== T_SEMICOLON
                ) {
                    $error = 'Space found before second semicolon of FOR loop';
                    $fix   = $phpcsFile->addFixableError($error, $secondSemicolon, 'SpacingBeforeSecond');
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($secondSemicolon - 1), '');
                    }
                }

                if (($secondSemicolon + 1) !== $closingBracket
                    && $tokens[($secondSemicolon + 1)]['code'] !== T_WHITESPACE
                ) {
                    $error = 'Expected 1 space after second semicolon of FOR loop; 0 found';
                    $fix   = $phpcsFile->addFixableError($error, $secondSemicolon, 'NoSpaceAfterSecond');
                    if ($fix === true) {
                        $phpcsFile->fixer->addContent($secondSemicolon, ' ');
                    }
                } else {
                    if (strlen($tokens[($secondSemicolon + 1)]['content']) !== 1) {
                        $spaces = strlen($tokens[($secondSemicolon + 1)]['content']);
                        $data   = [$spaces];
                        if (($secondSemicolon + 2) === $closingBracket) {
                            $error = 'Expected no space after second semicolon of FOR loop; %s found';
                            $fix   = $phpcsFile->addFixableError($error, $secondSemicolon, 'SpacingAfterSecondNoThird', $data);
                            if ($fix === true) {
                                $phpcsFile->fixer->replaceToken(($secondSemicolon + 1), '');
                            }
                        } else {
                            $error = 'Expected 1 space after second semicolon of FOR loop; %s found';
                            $fix   = $phpcsFile->addFixableError($error, $secondSemicolon, 'SpacingAfterSecond', $data);
                            if ($fix === true) {
                                $phpcsFile->fixer->replaceToken(($secondSemicolon + 1), ' ');
                            }
                        }
                    }
                }//end if
            }//end if
        }//end if

    }//end process()


}//end class
