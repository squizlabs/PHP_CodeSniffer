<?php
/**
 * Ensure that opacity values start with a 0 if it is not a whole number.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\CSS;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class OpacitySniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('CSS');


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return int[]
     */
    public function register()
    {
        return array(T_STYLE);

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where the token was found.
     * @param int                         $stackPtr  The position in the stack where
     *                                               the token was found.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['content'] !== 'opacity') {
            return;
        }

        $next = $phpcsFile->findNext(array(T_COLON, T_WHITESPACE), ($stackPtr + 1), null, true);

        if ($next === false
            || ($tokens[$next]['code'] !== T_DNUMBER
            && $tokens[$next]['code'] !== T_LNUMBER)
        ) {
            return;
        }

        $value = $tokens[$next]['content'];
        if ($tokens[$next]['code'] === T_LNUMBER) {
            if ($value !== '0' && $value !== '1') {
                $error = 'Opacity values must be between 0 and 1';
                $phpcsFile->addError($error, $next, 'Invalid');
            }
        } else {
            if (strlen($value) > 3) {
                $error = 'Opacity values must have a single value after the decimal point';
                $phpcsFile->addError($error, $next, 'DecimalPrecision');
            } else if ($value === '0.0' || $value === '1.0') {
                $error = 'Opacity value does not require decimal point; use %s instead';
                $data  = array($value{0});
                $fix   = $phpcsFile->addFixableError($error, $next, 'PointNotRequired', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($next, $value{0});
                }
            } else if ($value{0} === '.') {
                $error = 'Opacity values must not start with a decimal point; use 0%s instead';
                $data  = array($value);
                $fix   = $phpcsFile->addFixableError($error, $next, 'StartWithPoint', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($next, '0'.$value);
                }
            } else if ($value{0} !== '0') {
                $error = 'Opacity values must be between 0 and 1';
                $phpcsFile->addError($error, $next, 'Invalid');
            }//end if
        }//end if

    }//end process()


}//end class
