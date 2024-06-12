<?php
/**
 * Generic_Sniffs_Formatting_AlphabeticalPropertyNamesSniff.
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
 * Generic_Sniffs_Formatting_AlphabeticalPropertyNamesSniff.
 *
 * Ensures class properties are declared in alphabetical order.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Alex Howansky <alex.howansky@gmail.com>
 * @copyright 2016 Alex Howansky (https://github.com/AlexHowansky)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Formatting_AlphabeticalPropertyNamesSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Track the last property name we encountered.
     *
     * @var string
     */
    protected $lastPropertyName = null;


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
                T_VARIABLE,
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
        if ($tokens[$stackPtr]['code'] !== T_VARIABLE) {
            $this->lastPropertyName = null;
            return;
        }

        // We only care about class properties, so ignore global
        // variables (level 0) and method variables (level 2).
        if ($tokens[$stackPtr]['level'] === 1) {
            $propertyName = substr($tokens[$stackPtr]['content'], 1);
            if ($this->lastPropertyName !== null && $propertyName <= $this->lastPropertyName) {
                $phpcsFile->addError('Property "%s" is not in alphabetical order.', $stackPtr, 'Found', array($propertyName));
            }

            $this->lastPropertyName = $propertyName;
        }

    }//end process()


}//end class
