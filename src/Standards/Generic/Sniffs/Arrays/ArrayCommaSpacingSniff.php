<?php
/**
 * Ensure there is no space after and one after comma in array definition.
 *
 * @author    Vincent Langlet <vincentlanglet@example.com>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays;

use PHP_CodeSniffer\Sniffs\AbstractArraySniff;
use PHP_CodeSniffer\Util\Tokens;

class ArrayCommaSpacingSniff extends AbstractArraySniff
{

    /**
     * If the php version is < 7.3, we have to ignore hereDoc and nowDoc to avoid syntax errors.
     *
     * @var boolean
     */
    public $ignoreHereDocAndNowDoc = true;


    /**
     * Processes a single-line array definition.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile  The current file being checked.
     * @param int                         $stackPtr   The position of the current token
     *                                                in the stack passed in $tokens.
     * @param int                         $arrayStart The token that starts the array definition.
     * @param int                         $arrayEnd   The token that ends the array definition.
     * @param array                       $indices    An array of token positions for the array keys,
     *                                                double arrows, and values.
     *
     * @return void
     */
    public function processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices)
    {
        $this->processCommaCheck($phpcsFile, $stackPtr, $indices);

    }//end processSingleLineArray()


    /**
     * Processes a multi-line array definition.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile  The current file being checked.
     * @param int                         $stackPtr   The position of the current token
     *                                                in the stack passed in $tokens.
     * @param int                         $arrayStart The token that starts the array definition.
     * @param int                         $arrayEnd   The token that ends the array definition.
     * @param array                       $indices    An array of token positions for the array keys,
     *                                                double arrows, and values.
     *
     * @return void
     */
    public function processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices)
    {
        $this->processCommaCheck($phpcsFile, $stackPtr, $indices);

    }//end processMultiLineArray()


    /**
     * Processes a multi-line array definition.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being checked.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     * @param array                       $indices   An array of token positions for the array keys,
     *                                               double arrows, and values.
     *
     * @return void
     */
    private function processCommaCheck($phpcsFile, $stackPtr, $indices)
    {
        $tokens = $phpcsFile->getTokens();

        foreach ($indices as $index) {
            if (isset($index['comma']) === false) {
                continue;
            }

            $comma = $index['comma'];

            if (T_WHITESPACE !== $tokens[($comma + 1)]['code']) {
                $content = $tokens[($comma + 1)]['content'];
                $error   = 'Expected 1 space between comma and "%s"; 0 found';
                $data    = [$content];

                $fix = $phpcsFile->addFixableError($error, $comma, 'NoSpaceAfterComma', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->addContent($comma, ' ');
                }
            } else {
                $spaceLength = $tokens[($comma + 1)]['length'];
                if ($spaceLength > 1) {
                    $content = $tokens[($comma + 2)]['content'];
                    $error   = 'Expected 1 space between comma and "%s"; %s found';
                    $data    = [
                        $content,
                        $spaceLength,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $comma, 'SpaceAfterComma', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($comma + 1), ' ');
                    }
                }
            }//end if

            if ($tokens[($comma - 1)]['code'] === T_WHITESPACE) {
                if ($this->ignoreHereDocAndNowDoc === true) {
                    $previous     = $phpcsFile->findPrevious(T_WHITESPACE, ($comma - 1), $stackPtr, true);
                    $previousCode = $tokens[$previous]['code'];

                    if ($previousCode === T_END_HEREDOC || $previousCode === T_END_NOWDOC) {
                        return;
                    }
                }

                $content     = $tokens[($comma - 2)]['content'];
                $spaceLength = $tokens[($comma - 1)]['length'];
                $error       = 'Expected 0 spaces between "%s" and comma; %s found';
                $data        = [
                    $content,
                    $spaceLength,
                ];

                $fix = $phpcsFile->addFixableError($error, $comma, 'SpaceBeforeComma', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($comma - 1), '');
                }
            }//end if
        }//end foreach

    }//end processCommaCheck()


}//end class
