<?php
/**
 * Unit test class for the InlineComment sniff.
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
 * Unit test class for the InlineComment sniff.
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
class Squiz_Tests_Commenting_InlineCommentUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='InlineCommentUnitTest.inc')
    {
        switch ($testFile) {
        case 'InlineCommentUnitTest.inc':
            $errors = array(
                       17 => 1,
                       27 => 1,
                       28 => 1,
                       32 => 2,
                       36 => 1,
                       44 => 2,
                       54 => 1,
                       58 => 1,
                       61 => 1,
                       64 => 2,
                       67 => 1,
                       95 => 1,
                       96 => 1,
                       97 => 3,
                      );

            // The trait tests will only work in PHP version where traits exist and
            // will throw errors in earlier versions.
            if (version_compare(PHP_VERSION, '5.4.0') < 0) {
                $errors[106] = 1;
            }

            return $errors;
        case 'InlineCommentUnitTest.js':
            return array(
                    31  => 1,
                    36  => 2,
                    44  => 1,
                    48  => 1,
                    51  => 1,
                    54  => 2,
                    57  => 1,
                    102 => 1,
                    103 => 1,
                    104 => 3,
                   );
        default:
            return array();
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
