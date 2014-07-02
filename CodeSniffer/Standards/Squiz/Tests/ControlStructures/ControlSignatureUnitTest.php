<?php
/**
 * Unit test class for the ControlSignature sniff.
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
 * Unit test class for the ControlSignature sniff.
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
class Squiz_Tests_ControlStructures_ControlSignatureUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getErrorList($testFile='ControlSignatureUnitTest.inc')
    {
        switch ($testFile) {
        case 'ControlSignatureUnitTest.inc':
            return array(
                    9   => 1,
                    14  => 1,
                    20  => 1,
                    22  => 1,
                    32  => 1,
                    36  => 1,
                    44  => 1,
                    48  => 1,
                    56  => 1,
                    60  => 1,
                    68  => 1,
                    72  => 1,
                    84  => 1,
                    88  => 2,
                    100 => 1,
                    104 => 2,
                    116 => 2,
                    120 => 3,
                    122 => 1,
                    126 => 1,
                    130 => 1,
                    134 => 1,
                    139 => 1,
                    148 => 1,
                    152 => 1,
                    158 => 1,
                   );
        break;
        case 'ControlSignatureUnitTest.js':
            return array(
                    7   => 1,
                    12  => 1,
                    18  => 1,
                    20  => 1,
                    29  => 1,
                    33  => 1,
                    40  => 1,
                    44  => 1,
                    51  => 1,
                    55  => 1,
                    66  => 1,
                    70  => 2,
                    88  => 2,
                    92  => 3,
                    94  => 1,
                    98  => 1,
                    102 => 1,
                    106 => 1,
                    111 => 1,
                    120 => 1,
                    124 => 1,
                   );
            break;
        default:
            return array();
            break;
        }//end switch

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
        return array();

    }//end getWarningList()


}//end class

?>
