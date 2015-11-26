<?php
/**
 * Check for duplicate class definitions that can be merged into one.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\CSS;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DuplicateClassDefinitionSniff implements Sniff
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
        return array(T_OPEN_TAG);

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

        // Find the content of each class definition name.
        $classNames = array();
        $next       = $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, ($stackPtr + 1));
        if ($next === false) {
            // No class definitions in the file.
            return;
        }

        $find = array(
                 T_CLOSE_CURLY_BRACKET,
                 T_COMMENT,
                 T_OPEN_TAG,
                );

        while ($next !== false) {
            $prev = $phpcsFile->findPrevious($find, ($next - 1));

            // Create a sorted name for the class so we can compare classes
            // even when the individual names are all over the place.
            $name = '';
            for ($i = ($prev + 1); $i < $next; $i++) {
                $name .= $tokens[$i]['content'];
            }

            $name = trim($name);
            $name = str_replace("\n", ' ', $name);
            $name = preg_replace('|[\s]+|', ' ', $name);
            $name = str_replace(', ', ',', $name);

            $names = explode(',', $name);
            sort($names);
            $name = implode(',', $names);

            if (isset($classNames[$name]) === true) {
                $first = $classNames[$name];
                $error = 'Duplicate class definition found; first defined on line %s';
                $data  = array($tokens[$first]['line']);
                $phpcsFile->addError($error, $next, 'Found', $data);
            } else {
                $classNames[$name] = $next;
            }

            $next = $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, ($next + 1));
        }//end while

    }//end process()


}//end class
