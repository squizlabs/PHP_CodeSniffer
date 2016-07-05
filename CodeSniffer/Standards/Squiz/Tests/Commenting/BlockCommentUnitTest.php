<?php
/**
 * Unit test class for the BlockComment sniff.
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
 * Unit test class for the BlockComment sniff.
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
class Squiz_Tests_Commenting_BlockCommentUnitTest extends AbstractSniffUnitTest
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
        return array('--tab-width=4');

    }//end getCliValues()


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
        $errors = array(
                   8   => 1,
                   20  => 1,
                   24  => 1,
                   30  => 1,
                   31  => 1,
                   34  => 1,
                   40  => 1,
                   45  => 1,
                   49  => 1,
                   51  => 1,
                   53  => 1,
                   57  => 1,
                   60  => 1,
                   61  => 1,
                   63  => 1,
                   65  => 1,
                   68  => 1,
                   70  => 1,
                   72  => 1,
                   75  => 1,
                   84  => 1,
                   87  => 1,
                   89  => 1,
                   92  => 1,
                   111 => 1,
                   159 => 1,
                   181 => 1,
                   188 => 1,
                   206 => 1,
                   207 => 1,
                   214 => 1,
                  );

        return $errors;

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
