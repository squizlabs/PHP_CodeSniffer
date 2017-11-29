<?php
/**
 * Ensures all calls to inbuilt PHP functions are lowercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class LowercasePHPFunctionsSniff implements Sniff
{

    /**
     * String -> int hash map of all php built in function names
     *
     * @var array
     */
    private $builtInFunctions;


    /**
     * Construct the LowercasePHPFunctionSniff
     */
    public function __construct()
    {

        $allFunctions           = get_defined_functions();
        $this->builtInFunctions = array_flip($allFunctions['internal']);

    }//end __construct()


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_STRING];

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
        $tokens = $phpcsFile->getTokens();

        // Make sure this is a function call.
        $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($next === false) {
            // Not a function call.
            return;
        }

        if ($tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
            // Not a function call.
            return;
        }

        $prev = $phpcsFile->findPrevious([T_WHITESPACE, T_BITWISE_AND], ($stackPtr - 1), null, true);
        if ($tokens[$prev]['code'] === T_FUNCTION) {
            // Function declaration, not a function call.
            return;
        }

        if ($tokens[$prev]['code'] === T_NS_SEPARATOR) {
            // Namespaced class/function, not an inbuilt function.
            return;
        }

        if ($tokens[$prev]['code'] === T_NEW) {
            // Object creation, not an inbuilt function.
            return;
        }

        if ($tokens[$prev]['code'] === T_OBJECT_OPERATOR) {
            // Not an inbuilt function.
            return;
        }

        if ($tokens[$prev]['code'] === T_DOUBLE_COLON) {
            // Not an inbuilt function.
            return;
        }

        // Make sure it is an inbuilt PHP function.
        // PHP_CodeSniffer can possibly include user defined functions
        // through the use of vendor/autoload.php.
        $content = $tokens[$stackPtr]['content'];
        if (isset($this->builtInFunctions[strtolower($content)]) === false) {
            return;
        }

        if ($content !== strtolower($content)) {
            $error = 'Calls to inbuilt PHP functions must be lowercase; expected "%s" but found "%s"';
            $data  = [
                strtolower($content),
                $content,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'CallUppercase', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, strtolower($content));
            }
        }

    }//end process()


}//end class
