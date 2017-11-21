<?php
/**
 * Ensure that browser-specific styles are not used.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\MySource\Sniffs\CSS;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class BrowserSpecificStylesSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['CSS'];

    /**
     * A list of specific stylesheet suffixes we allow.
     *
     * These stylesheets contain browser specific styles
     * so this sniff ignore them files in the form:
     * *_moz.css and *_ie7.css etc.
     *
     * @var array
     */
    protected $specificStylesheets = [
        'moz'    => true,
        'ie'     => true,
        'ie7'    => true,
        'ie8'    => true,
        'webkit' => true,
    ];


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_STYLE];

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
        // Ignore files with browser-specific suffixes.
        $filename  = $phpcsFile->getFilename();
        $breakChar = strrpos($filename, '_');
        if ($breakChar !== false && substr($filename, -4) === '.css') {
            $specific = substr($filename, ($breakChar + 1), -4);
            if (isset($this->specificStylesheets[$specific]) === true) {
                return;
            }
        }

        $tokens  = $phpcsFile->getTokens();
        $content = $tokens[$stackPtr]['content'];

        if ($content{0} === '-') {
            $error = 'Browser-specific styles are not allowed';
            $phpcsFile->addError($error, $stackPtr, 'ForbiddenStyle');
        }

    }//end process()


}//end class
