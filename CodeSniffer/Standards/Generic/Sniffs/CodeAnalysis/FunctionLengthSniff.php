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
 * This sniff class detects the length of functions.
 *
 * This sniff counts the number of lines in a function. This allows to detect overly long functions, that
 * might need to be split up into several.
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
class Generic_Sniffs_CodeAnalysis_FunctionLengthSniff implements PHP_CodeSniffer_Sniff
{
    public $warnAtLines  = 30;
    public $errorAtLines = 60;


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return array(T_FUNCTION);

    }//end register()


    /**
     * Processes a token that is found within the scope that this test is
     * listening to.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param integer              $stackPtr  The position in the stack where this
     *                                        token was found.
     *
     * @return void
     */
    public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr )
    {
        $numberOfLines = self::calculateNumberOfLines($phpcsFile, $stackPtr);

        if (is_null($this->errorAtLines) === false
            && $numberOfLines >= $this->errorAtLines
        ) {
            $error = 'A method may not exceed %s lines, but this is %s long.';
            $data  = array(
                      $this->errorAtLines,
                      $numberOfLines,
                     );

            $phpcsFile->addError($error, $stackPtr, '', $data);
        } else if (is_null($this->warnAtLines) === false
            &&$numberOfLines >= $this->warnAtLines
        ) {
            $warning = 'This method is quite long, maybe you want to split it up?';
            $data    = array(
                        $this->warnAtLines,
                        $numberOfLines,
                       );

            $phpcsFile->addWarning($warning, $stackPtr, '', $data);
        }

        // Record metrics for common groupings of function length.
        $phpcsFile->recordGroupMetric($stackPtr, 'Function Length', $numberOfLines, array(25, 50, 100, 150));

    }//end process()


    /**
     * Return the number of lines between the (functions) opening an closing brackets.
     *
     * @param PHP_CodeSniffer_File $phpcsFile    The file where this token was found.
     * @param integer              $stackPointer The position in the stack where this
     *                                        token was found.
     *
     * @return int
     */
    protected static function calculateNumberOfLines( PHP_CodeSniffer_File $phpcsFile, $stackPointer )
    {
        $tokenSet      = $phpcsFile->getTokens();
        $functionToken = $tokenSet[$stackPointer];

        $openingPosition = $functionToken['scope_opener'];
        $closingPosition = $functionToken['scope_closer'];

        $openingToken = $tokenSet[$openingPosition];
        $closingToken = $tokenSet[$closingPosition];

        return ($closingToken['line'] - $openingToken['line']);

    }//end calculateNumberOfLines()


}//end class
