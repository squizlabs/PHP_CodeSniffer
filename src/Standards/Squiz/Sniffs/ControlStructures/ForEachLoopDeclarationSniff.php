<?php
/**
 * Verifies that there is a space between each condition of foreach loops.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ForEachLoopDeclarationSniff implements Sniff
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
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_FOREACH];

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
            $error = 'Possible parse error: FOREACH has no opening parenthesis';
            $phpcsFile->addWarning($error, $stackPtr, 'MissingOpenParenthesis');
            return;
        }

        if (isset($tokens[$openingBracket]['parenthesis_closer']) === false) {
            $error = 'Possible parse error: FOREACH has no closing parenthesis';
            $phpcsFile->addWarning($error, $stackPtr, 'MissingCloseParenthesis');
            return;
        }

        $closingBracket = $tokens[$openingBracket]['parenthesis_closer'];

        if ($this->requiredSpacesAfterOpen === 0 && $tokens[($openingBracket + 1)]['code'] === T_WHITESPACE) {
            $error = 'Space found after opening bracket of FOREACH loop';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterOpen');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken(($openingBracket + 1), '');
            }
        } else if ($this->requiredSpacesAfterOpen > 0) {
            $spaceAfterOpen = 0;
            if ($tokens[($openingBracket + 1)]['code'] === T_WHITESPACE) {
                $spaceAfterOpen = $tokens[($openingBracket + 1)]['length'];
            }

            if ($spaceAfterOpen !== $this->requiredSpacesAfterOpen) {
                $error = 'Expected %s spaces after opening bracket; %s found';
                $data  = [
                    $this->requiredSpacesAfterOpen,
                    $spaceAfterOpen,
                ];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfterOpen', $data);
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
            $error = 'Space found before closing bracket of FOREACH loop';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeClose');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken(($closingBracket - 1), '');
            }
        } else if ($this->requiredSpacesBeforeClose > 0) {
            $spaceBeforeClose = 0;
            if ($tokens[($closingBracket - 1)]['code'] === T_WHITESPACE) {
                $spaceBeforeClose = $tokens[($closingBracket - 1)]['length'];
            }

            if ($spaceBeforeClose !== $this->requiredSpacesBeforeClose) {
                $error = 'Expected %s spaces before closing bracket; %s found';
                $data  = [
                    $this->requiredSpacesBeforeClose,
                    $spaceBeforeClose,
                ];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeClose', $data);
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

        $asToken = $phpcsFile->findNext(T_AS, $openingBracket);
        if ($asToken === false) {
            $error = 'Possible parse error: FOREACH has no AS statement';
            $phpcsFile->addWarning($error, $stackPtr, 'MissingAs');
            return;
        }

        $content = $tokens[$asToken]['content'];
        if ($content !== strtolower($content)) {
            $expected = strtolower($content);
            $error    = 'AS keyword must be lowercase; expected "%s" but found "%s"';
            $data     = [
                $expected,
                $content,
            ];

            $fix = $phpcsFile->addFixableError($error, $asToken, 'AsNotLower', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($asToken, $expected);
            }
        }

        $doubleArrow = $phpcsFile->findNext(T_DOUBLE_ARROW, $asToken, $closingBracket);

        if ($doubleArrow !== false) {
            if ($tokens[($doubleArrow - 1)]['code'] !== T_WHITESPACE) {
                $error = 'Expected 1 space before "=>"; 0 found';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBeforeArrow');
                if ($fix === true) {
                    $phpcsFile->fixer->addContentBefore($doubleArrow, ' ');
                }
            } else {
                if ($tokens[($doubleArrow - 1)]['length'] !== 1) {
                    $spaces = $tokens[($doubleArrow - 1)]['length'];
                    $error  = 'Expected 1 space before "=>"; %s found';
                    $data   = [$spaces];
                    $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBeforeArrow', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($doubleArrow - 1), ' ');
                    }
                }
            }

            if ($tokens[($doubleArrow + 1)]['code'] !== T_WHITESPACE) {
                $error = 'Expected 1 space after "=>"; 0 found';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfterArrow');
                if ($fix === true) {
                    $phpcsFile->fixer->addContent($doubleArrow, ' ');
                }
            } else {
                if ($tokens[($doubleArrow + 1)]['length'] !== 1) {
                    $spaces = $tokens[($doubleArrow + 1)]['length'];
                    $error  = 'Expected 1 space after "=>"; %s found';
                    $data   = [$spaces];
                    $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfterArrow', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($doubleArrow + 1), ' ');
                    }
                }
            }
        }//end if

        if ($tokens[($asToken - 1)]['code'] !== T_WHITESPACE) {
            $error = 'Expected 1 space before "as"; 0 found';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBeforeAs');
            if ($fix === true) {
                $phpcsFile->fixer->addContentBefore($asToken, ' ');
            }
        } else {
            if ($tokens[($asToken - 1)]['length'] !== 1) {
                $spaces = $tokens[($asToken - 1)]['length'];
                $error  = 'Expected 1 space before "as"; %s found';
                $data   = [$spaces];
                $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBeforeAs', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($asToken - 1), ' ');
                }
            }
        }

        if ($tokens[($asToken + 1)]['code'] !== T_WHITESPACE) {
            $error = 'Expected 1 space after "as"; 0 found';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfterAs');
            if ($fix === true) {
                $phpcsFile->fixer->addContent($asToken, ' ');
            }
        } else {
            if ($tokens[($asToken + 1)]['length'] !== 1) {
                $spaces = $tokens[($asToken + 1)]['length'];
                $error  = 'Expected 1 space after "as"; %s found';
                $data   = [$spaces];
                $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfterAs', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($asToken + 1), ' ');
                }
            }
        }

    }//end process()


}//end class
