<?php
/**
 * Unit test class for the DisallowTabIndent sniff.
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
 * Unit test class for the DisallowTabIndent sniff.
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
class Generic_Tests_WhiteSpace_DisallowTabIndentUnitTest extends AbstractSniffUnitTest
{

    /**
     * Get a list of CLI values to set befor the file is tested.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array
     */
    public function getCliValues($testFile)
    {
        return array('--tab-width=4', '--encoding=utf-8');

    }//end getCliValues()


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
    public function getErrorList($testFile='DisallowTabIndentUnitTest.inc')
    {
        switch ($testFile) {
        case 'DisallowTabIndentUnitTest.inc':
            return array(
                    5  => 2,
                    9  => 1,
                    15 => 1,
                    20 => 2,
                    21 => 1,
                    22 => 2,
                    23 => 1,
                    24 => 2,
                    31 => 1,
                    32 => 2,
                    33 => 2,
                    41 => 1,
                    42 => 1,
                    43 => 1,
                    44 => 1,
                    45 => 1,
                    46 => 1,
                    47 => 1,
                    48 => 1,
                   );
            break;
        case 'DisallowTabIndentUnitTest.js':
            return array(
                    3 => 1,
                    5 => 1,
                    6 => 1,
                   );
            break;
        case 'DisallowTabIndentUnitTest.css':
            return array(
                    1 => 1,
                    2 => 1,
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
