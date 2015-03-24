<?php
/**
 * Unit test class for the ValidVariableName sniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Unit test class for the ValidVariableName sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Zend_Tests_NamingConventions_ValidVariableNameUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getErrorList()
    {
        return array(
                3  => 1,
                5  => 1,
                11 => 1,
                13 => 1,
                17 => 1,
                19 => 1,
                23 => 1,
                25 => 1,
                29 => 1,
                31 => 1,
                36 => 1,
                38 => 1,
                42 => 1,
                44 => 1,
                48 => 1,
                50 => 1,
                61 => 1,
                67 => 1,
                72 => 1,
                74 => 1,
                75 => 1,
                76 => 1,
                79 => 1,
                96 => 1,
                99 => 1,
               );

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getWarningList()
    {
        return array(
                6  => 1,
                14 => 1,
                20 => 1,
                26 => 1,
                32 => 1,
                39 => 1,
                45 => 1,
                51 => 1,
                64 => 1,
                70 => 1,
                73 => 1,
                76 => 1,
                79 => 1,
                82 => 1,
                94 => 1,
               );

    }//end getWarningList()


}//end class

?>
