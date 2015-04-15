<?php
/**
 * This file is part of the CodeAnalysis add-on for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2007-2014 Manuel Pichler. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * This sniff class detects the number of methods in a class or interface.
 *
 * This sniff counts the number of methods in a class or interface. This allows to detect overly long classes, that
 * might need to be refactored.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2007-2014 Manuel Pichler. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_CodeAnalysis_NumberOfMethodsInClassSniff implements PHP_CodeSniffer_Sniff
{
    public $warnAt  = 15;
    public $errorAt = 30;


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * An example return value for a sniff that wants to listen for whitespace
     * and any comments would be:
     *
     * <code>
     *    return array(
     *            T_WHITESPACE,
     *            T_DOC_COMMENT,
     *            T_COMMENT,
     *           );
     * </code>
     *
     * @return int[]
     * @see    Tokens.php
     */
    public function register()
    {
        return array(
                T_CLASS,
                T_INTERFACE,
               );

    }//end register()


    /**
     * Called when one of the token types that this sniff is listening for
     * is found.
     *
     * The stackPtr variable indicates where in the stack the token was found.
     * A sniff can acquire information this token, along with all the other
     * tokens within the stack by first acquiring the token stack:
     *
     * <code>
     *    $tokens = $phpcsFile->getTokens();
     *    echo 'Encountered a '.$tokens[$stackPtr]['type'].' token';
     *    echo 'token information: ';
     *    print_r($tokens[$stackPtr]);
     * </code>
     *
     * If the sniff discovers an anomaly in the code, they can raise an error
     * by calling addError() on the PHP_CodeSniffer_File object, specifying an error
     * message and the position of the offending token:
     *
     * <code>
     *    $phpcsFile->addError('Encountered an error', $stackPtr);
     * </code>
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where the
     *                                        token was found.
     * @param int                  $stackPtr  The position in the PHP_CodeSniffer
     *                                        file's token stack where the token
     *                                        was found.
     *
     * @return void
     */
    public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr )
    {
        $numberOfMethods = self::computeNumberOfMethodsInClass($phpcsFile, $stackPtr);

        if (is_null($this->errorAt) === false
            && $numberOfMethods >= $this->errorAt
        ) {
            $error = 'This class/interface has %s methods but may not contain more than %s. ';
            $data  = array(
                      $numberOfMethods,
                      $this->errorAt,
                     );

            $phpcsFile->addError($error, $stackPtr, '', $data);
        } else if (is_null($this->warnAt) === false
            && $numberOfMethods >= $this->warnAt
        ) {
            $warning = 'This class/interface has %s methods which is more then the suggested %s. Consider splitting it up.';
            $data    = array(
                        $numberOfMethods,
                        $this->warnAt,
                       );

            $phpcsFile->addWarning($warning, $stackPtr, '', $data);
        }

        // Record metrics for common groupings of method numbers.
        $phpcsFile->recordGroupMetric($stackPtr, 'Number of Methods', $numberOfMethods, array(10, 20, 30, 40));

    }//end process()


    /**
     * Return the number of methods in this class or interface.
     *
     * @param PHP_CodeSniffer_File $phpcsFile    The PHP_CodeSniffer file where the
     *                                        token was found.
     * @param int                  $stackPointer The position in the PHP_CodeSniffer
     *                                        file's token stack where the token
     *                                        was found.
     *
     * @return int
     */
    protected static function computeNumberOfMethodsInClass(PHP_CodeSniffer_File $phpcsFile, $stackPointer)
    {
        // Find the end of the class/interface. We can terminate there.
        $tokenSet   = $phpcsFile->getTokens();
        $classToken = $tokenSet[$stackPointer];

        $openingPosition = $classToken['scope_opener'];
        $closingPosition = $classToken['scope_closer'];

        $tokens = $phpcsFile->getTokens();

        $curPointer = $openingPosition;
        $numMethods = 0;
        while (isset($tokens[$curPointer]) && $curPointer < $closingPosition) {
            // Skip closures.
            if ($tokens[$curPointer]['code'] === T_FUNCTION
                && isset($tokens[$curPointer]['conditions'][$stackPointer]) === true
            ) {
                $numMethods++;
            }

            $curPointer++;
        }

        return $numMethods;

    }//end computeNumberOfMethodsInClass()


}//end class
