<?php
/**
 * Makes sure there are no spaces around the concatenation operator.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class ConcatenationSpacingSniff implements Sniff
{

    /**
     * The number of spaces before and after a string concat.
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
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING_CONCAT);

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

        $ignoreBefore = false;
        $prev         = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($tokens[$prev]['code'] === T_END_HEREDOC || $tokens[$prev]['code'] === T_END_NOWDOC) {
            // Spacing before must be preserved due to the here/nowdoc closing tag.
            $ignoreBefore = true;
        }

        $this->spacing = (int) $this->spacing;

        if ($ignoreBefore === false) {
            if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE) {
                $before = 0;
            } else {
                if ($tokens[($stackPtr - 2)]['line'] !== $tokens[$stackPtr]['line']) {
                    $before = 'newline';
                } else {
                    $before = $tokens[($stackPtr - 1)]['length'];
                }
            }

            $phpcsFile->recordMetric($stackPtr, 'Spacing before string concat', $before);
        }

        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            $after = 0;
        } else {
            if ($tokens[($stackPtr + 2)]['line'] !== $tokens[$stackPtr]['line']) {
                $after = 'newline';
            } else {
                $after = $tokens[($stackPtr + 1)]['length'];
            }
        }

        $phpcsFile->recordMetric($stackPtr, 'Spacing after string concat', $after);

        if (($ignoreBefore === true
            || $before === $this->spacing
            || ($before === 'newline'
            && $this->ignoreNewlines === true))
            && ($after === $this->spacing
            || ($after === 'newline'
            && $this->ignoreNewlines === true))
        ) {
            return;
        }

        if ($this->spacing === 0) {
            $message = 'Concat operator must not be surrounded by spaces';
            $data    = array();
        } else {
            if ($this->spacing > 1) {
                $message = 'Concat operator must be surrounded by %s spaces';
            } else {
                $message = 'Concat operator must be surrounded by a single space';
            }

            $data = array($this->spacing);
        }

        $fix = $phpcsFile->addFixableError($message, $stackPtr, 'PaddingFound', $data);

        if ($fix === true) {
            $padding = str_repeat(' ', $this->spacing);
            if ($ignoreBefore === false && ($before !== 'newline' || $this->ignoreNewlines === false)) {
                if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken(($stackPtr - 1), $padding);
                    if ($this->spacing === 0
                        && ($tokens[($stackPtr - 2)]['code'] === T_LNUMBER
                        || $tokens[($stackPtr - 2)]['code'] === T_DNUMBER)
                    ) {
                        $phpcsFile->fixer->replaceToken(($stackPtr - 2), '('.$tokens[($stackPtr - 2)]['content'].')');
                    }

                    $phpcsFile->fixer->endChangeset();
                } else if ($this->spacing > 0) {
                    $phpcsFile->fixer->addContent(($stackPtr - 1), $padding);
                }
            }

            if ($after !== 'newline' || $this->ignoreNewlines === false) {
                if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), $padding);
                    if ($this->spacing === 0
                        && ($tokens[($stackPtr + 2)]['code'] === T_LNUMBER
                        || $tokens[($stackPtr + 2)]['code'] === T_DNUMBER)
                    ) {
                        $phpcsFile->fixer->replaceToken(($stackPtr + 2), '('.$tokens[($stackPtr + 2)]['content'].')');
                    }

                    $phpcsFile->fixer->endChangeset();
                } else if ($this->spacing > 0) {
                    $phpcsFile->fixer->addContent($stackPtr, $padding);
                }
            }
        }//end if

    }//end process()


}//end class
