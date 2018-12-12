<?php
/**
 * Ensures there is a single space after a NOT operator.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class SpaceAfterNotSniff implements Sniff
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
     * The number of spaces desired after the NOT operator.
     *
     * @var integer
     */
    public $spacing = 1;

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
        return [T_BOOLEAN_NOT];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens        = $phpcsFile->getTokens();
        $this->spacing = (int) $this->spacing;

        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($nextNonEmpty === false) {
            return;
        }

        if ($this->ignoreNewlines === true
            && $tokens[$stackPtr]['line'] !== $tokens[$nextNonEmpty]['line']
        ) {
            return;
        }

        if ($this->spacing === 0 && $nextNonEmpty === ($stackPtr + 1)) {
            return;
        }

        $nextNonWhitespace = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($nextNonEmpty !== $nextNonWhitespace) {
            $error = 'Expected %s space(s) after NOT operator; comment found';
            $data  = [$this->spacing];
            $phpcsFile->addError($error, $stackPtr, 'CommentFound', $data);
            return;
        }

        $found = 0;
        if ($tokens[$stackPtr]['line'] !== $tokens[$nextNonEmpty]['line']) {
            $found = 'newline';
        } else if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
            $found = $tokens[($stackPtr + 1)]['length'];
        }

        if ($found === $this->spacing) {
            return;
        }

        $error = 'Expected %s space(s) after NOT operator; %s found';
        $data  = [
            $this->spacing,
            $found,
        ];

        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Incorrect', $data);
        if ($fix === true) {
            $padding = str_repeat(' ', $this->spacing);
            if ($found === 0) {
                $phpcsFile->fixer->addContent($stackPtr, $padding);
            } else {
                $phpcsFile->fixer->beginChangeset();
                $start = ($stackPtr + 1);

                if ($this->spacing > 0) {
                    $phpcsFile->fixer->replaceToken($start, $padding);
                    ++$start;
                }

                for ($i = $start; $i < $nextNonWhitespace; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }

    }//end process()


}//end class
