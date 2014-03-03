<?php
/**
 * Unit test class for the ArrayDeclaration sniff.
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
 * Unit test class for the ArrayDeclaration sniff.
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
class Squiz_Tests_Arrays_ArrayDeclarationUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getErrorList()
    {
        return array(
                7   => 2,
                9   => 2,
                22  => 1,
                23  => 1,
                24  => 1,
                25  => 1,
                31  => 1,
                35  => 1,
                36  => 1,
                41  => 1,
                46  => 1,
                47  => 1,
                50 =>  1,
                51  => 1,
                53  => 1,
                56  => 1,
                58  => 1,
                61  => 1,
                62  => 1,
                63  => 1,
                64  => 1,
                65  => 1,
                66  => 3,
                70  => 1,
                76  => 2,
                77  => 1,
                78  => 7,
                79  => 2,
                81  => 2,
                82  => 4,
                87  => 1,
                88  => 1,
                92  => 1,
                97  => 1,
                100 => 1,
                101 => 1,
                102 => 1,
                105 => 1,
                106 => 1,
                107 => 1,
                125 => 1,
                126 => 1,
                141 => 1,
                144 => 1,
                146 => 1,
                148 => 1,
                151 => 1,
                157 => 1,
                174 => 3,
                179 => 1,
                182 => 1,
                188 => 1,
                201 => 1,
               );

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getWarningList()
    {
        return array();

    }//end getWarningList()


}//end class

?>
