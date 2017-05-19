<?php
/**
 * Generic_Sniffs_Formatting_AlphabeticalMethodNamesSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Alex Howansky <alex.howansky@gmail.com>
 * @copyright 2016 Alex Howansky (https://github.com/AlexHowansky)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_Formatting_AlphabeticalMethodNamesSniff.
 *
 * Ensures class methods are declared in alphabetical order.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Alex Howansky <alex.howansky@gmail.com>
 * @copyright 2016 Alex Howansky (https://github.com/AlexHowansky)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Formatting_AlphabeticalMethodNamesSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Track the last method name we encountered.
     *
     * @var string
     */
    protected $lastMethodName = null;


    /**
     * Register the tokens we're interested in.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CLASS,
                T_INTERFACE,
                T_TRAIT,
                T_FUNCTION,
               );

    }//end register()


    /**
     * Process a token.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // In case we have more than one class/interface/trait
        // per file, we want to reset at the start of each.
        if ($tokens[$stackPtr]['code'] !== T_FUNCTION) {
            $this->lastMethodName = null;
            return;
        }

        // We only care about class methods, so ignore global functions (level 0).
        if ($tokens[$stackPtr]['level'] === 1) {
            $methodName = $phpcsFile->getDeclarationName($stackPtr);
            if ($this->lastMethodName !== null && $methodName <= $this->lastMethodName) {
                $phpcsFile->addError('Method "%s" is not in alphabetical order.', $stackPtr, 'Found', array($methodName));
            }

            $this->lastMethodName = $methodName;
        }

    }//end process()


}//end class
