<?php
/**
 * Unit test class for the MultipleStatementAlignment sniff.
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
 * Unit test class for the MultipleStatementAlignment sniff.
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
class Generic_Tests_Formatting_MultipleStatementAlignmentUnitTest extends AbstractSniffUnitTest
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
        return array();

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array(int => int)
     */
    public function getWarningList($testFile='MultipleStatementAlignmentUnitTest.inc')
    {
        switch ($testFile) {
        case 'MultipleStatementAlignmentUnitTest.inc':
            return array(
                    11  => 1,
                    12  => 1,
                    23  => 1,
                    24  => 1,
                    26  => 1,
                    27  => 1,
                    37  => 1,
                    38  => 1,
                    48  => 1,
                    50  => 1,
                    61  => 1,
                    62  => 1,
                    64  => 1,
                    65  => 1,
                    71  => 1,
                    78  => 1,
                    79  => 1,
                    86  => 1,
                    92  => 1,
                    93  => 1,
                    94  => 1,
                    95  => 1,
                    123 => 1,
                    124 => 1,
                    126 => 1,
                    129 => 1,
                    154 => 1,
                    161 => 1,
                    178 => 1,
                    179 => 1,
                    182 => 1,
                   );
        break;
        case 'MultipleStatementAlignmentUnitTest.js':
            return array(
                    11  => 1,
                    12  => 1,
                    23  => 1,
                    24  => 1,
                    26  => 1,
                    27  => 1,
                    37  => 1,
                    38  => 1,
                    48  => 1,
                    50  => 1,
                    61  => 1,
                    62  => 1,
                    64  => 1,
                    65  => 1,
                    71  => 1,
                    78  => 1,
                    79  => 1,
                    81  => 1,
                    82  => 1,
                    83  => 1,
                    85  => 1,
                    86  => 1,
                   );
            break;
        default:
            return array();
            break;
        }

    }//end getWarningList()


}//end class

?>
