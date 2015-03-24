<?php

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Sniff;

/**
 * Generic_Sniffs_PHP_NoSilencedErrorsSniff
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Andy Brockhurst <abrock@yahoo-inc.com>
 * @license  https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_PHP_NoSilencedErrorsSniff.
 *
 * Throws an error or warning when any code prefixed with an asperand is encountered.
 *
 * <code>
 *  if (@in_array($array, $needle))
 *  {
 *      doSomething();
 *  }
 * </code>
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Andy Brockhurst <abrock@yahoo-inc.com>
 * @license  https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class NoSilencedErrorsSniff implements Sniff
{

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    public $error = false;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_ASPERAND);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process($phpcsFile, $stackPtr)
    {
        $error = 'Silencing errors is forbidden';
        if ($this->error === true) {
            $error = 'Silencing errors is forbidden';
            $phpcsFile->addError($error, $stackPtr, 'Forbidden');
        } else {
            $error = 'Silencing errors is discouraged';
            $phpcsFile->addWarning($error, $stackPtr, 'Discouraged');
        }

    }//end process()


}//end class
