<?php
/**
 * Checks that no Perl-style comments are used.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Sniffs\Commenting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class InlineCommentSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_COMMENT];

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

        if ($tokens[$stackPtr]['content'][0] === '#') {
            $phpcsFile->recordMetric($stackPtr, 'Inline comment style', '# ...');

            $error  = 'Perl-style comments are not allowed. Use "// Comment."';
            $error .= ' or "/* comment */" instead.';
            $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'WrongStyle');
            if ($fix === true) {
                $newComment = ltrim($tokens[$stackPtr]['content'], '# ');
                $newComment = '// '.$newComment;
                $phpcsFile->fixer->replaceToken($stackPtr, $newComment);
            }
        } else if ($tokens[$stackPtr]['content'][0] === '/'
            && $tokens[$stackPtr]['content'][1] === '/'
        ) {
            $phpcsFile->recordMetric($stackPtr, 'Inline comment style', '// ...');
        } else if ($tokens[$stackPtr]['content'][0] === '/'
            && $tokens[$stackPtr]['content'][1] === '*'
        ) {
            $phpcsFile->recordMetric($stackPtr, 'Inline comment style', '/* ... */');
        }

    }//end process()


}//end class
