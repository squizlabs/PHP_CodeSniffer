<?php
/**
 * Unit test class for the LongConditionClosingComment sniff.
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
 * Unit test class for the LongConditionClosingComment sniff.
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
class Squiz_Tests_Commenting_LongConditionClosingCommentUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='LongConditionClosingCommentUnitTest.inc')
    {
        switch ($testFile) {
        case 'LongConditionClosingCommentUnitTest.inc':
            return array(
                    49  => 1,
                    99  => 1,
                    146 => 1,
                    192 => 1,
                    215 => 1,
                    238 => 1,
                    261 => 1,
                    286 => 1,
                    309 => 1,
                    332 => 1,
                    355 => 1,
                    378 => 1,
                    493 => 1,
                    531 => 1,
                    536 => 1,
                    540 => 1,
                    562 => 1,
                    601 => 1,
                    629 => 1,
                    663 => 1,
                    765 => 1,
                    798 => 1,
                    811 => 1,
                    897 => 1,
                    931 => 1,
                   );
            break;
        case 'LongConditionClosingCommentUnitTest.js':
            return array(
                    47  => 1,
                    97  => 1,
                    144 => 1,
                    190 => 1,
                    213 => 1,
                    238 => 1,
                    261 => 1,
                    284 => 1,
                    307 => 1,
                    401 => 1,
                    439 => 1,
                    444 => 1,
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
