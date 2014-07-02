<?php
/**
 * Unit test class for the OperatorSpacing sniff.
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
 * Unit test class for the OperatorSpacing sniff.
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
class Squiz_Tests_WhiteSpace_OperatorSpacingUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='OperatorSpacingUnitTest.inc')
    {
        switch ($testFile) {
        case 'OperatorSpacingUnitTest.inc':
            return array(
                    4  => 1,
                    5  => 2,
                    6  => 1,
                    7  => 1,
                    8  => 2,
                    11 => 1,
                    12 => 2,
                    13 => 1,
                    14 => 1,
                    15 => 2,
                    18 => 1,
                    19 => 2,
                    20 => 1,
                    21 => 1,
                    22 => 2,
                    25 => 1,
                    26 => 2,
                    27 => 1,
                    28 => 1,
                    29 => 2,
                    32 => 1,
                    33 => 2,
                    34 => 1,
                    35 => 1,
                    36 => 2,
                    40 => 2,
                    42 => 2,
                    44 => 2,
                    45 => 1,
                    46 => 2,
                    53 => 2,
                    54 => 1,
                    59 => 10,
                    64 => 1,
                    77 => 4,
                    78 => 1,
                    79 => 1,
                    80 => 2,
                    81 => 1,
                    84 => 6,
                    85 => 6,
                    87 => 4,
                    88 => 5,
                    90 => 4,
                    91 => 5,
                    128 => 4,
                   );
            break;
        case 'OperatorSpacingUnitTest.js':
            return array(
                    4  => 1,
                    5  => 2,
                    6  => 1,
                    7  => 1,
                    8  => 2,
                    11 => 1,
                    12 => 2,
                    13 => 1,
                    14 => 1,
                    15 => 2,
                    18 => 1,
                    19 => 2,
                    20 => 1,
                    21 => 1,
                    22 => 2,
                    25 => 1,
                    26 => 2,
                    27 => 1,
                    28 => 1,
                    29 => 2,
                    32 => 1,
                    33 => 2,
                    34 => 1,
                    35 => 1,
                    36 => 2,
                    40 => 2,
                    42 => 2,
                    44 => 2,
                    45 => 1,
                    46 => 2,
                    55 => 4,
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
