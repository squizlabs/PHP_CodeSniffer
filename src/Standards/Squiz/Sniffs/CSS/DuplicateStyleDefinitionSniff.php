<?php
/**
 * Check for duplicate style definitions in the same class.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\CSS;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DuplicateStyleDefinitionSniff implements Sniff
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
        return array(T_OPEN_CURLY_BRACKET);

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Find the content of each style definition name.
        $end  = $tokens[$stackPtr]['bracket_closer'];
        $next = $phpcsFile->findNext(T_STYLE, ($stackPtr + 1), $end);
        if ($next === false) {
            // Class definition is empty.
            return;
        }

        $styleNames = array();

        while ($next !== false) {
            $name = $tokens[$next]['content'];
            if (isset($styleNames[$name]) === true) {
                $first = $styleNames[$name];
                $error = 'Duplicate style definition found; first defined on line %s';
                $data  = array($tokens[$first]['line']);
                $phpcsFile->addError($error, $next, 'Found', $data);
            } else {
                $styleNames[$name] = $next;
            }

            $next = $phpcsFile->findNext(T_STYLE, ($next + 1), $end);
        }//end while

    }//end process()


}//end class
