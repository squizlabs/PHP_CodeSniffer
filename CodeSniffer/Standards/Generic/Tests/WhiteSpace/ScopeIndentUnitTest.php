<?php
/**
 * Unit test class for the ScopeIndent sniff.
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
 * Unit test class for the ScopeIndent sniff.
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
class Generic_Tests_WhiteSpace_ScopeIndentUnitTest extends AbstractSniffUnitTest
{


    /**
     * Get a list of CLI values to set befor the file is tested.
     *
     * @param string $filename The name of the file being tested.
     *
     * @return array
     */
    public function getCliValues($filename)
    {
        // Tab width setting is only needed for the tabbed file.
        if ($filename === 'ScopeIndentUnitTest.2.inc') {
            return array('--tabWidth=4');
        }

        return array('--tabWidth=0');

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
        return array(
                7   => 1,
                10  => 1,
                13  => 1,
                17  => 1,
                20  => 1,
                24  => 1,
                25  => 1,
                27  => 1,
                28  => 1,
                29  => 1,
                30  => 1,
                58  => 1,
                123 => 1,
                224 => 1,
                225 => 1,
                279 => 1,
                280 => 1,
                281 => 1,
                284 => 1,
                336 => 1,
                349 => 1,
                380 => 1,
                386 => 1,
                387 => 1,
                388 => 1,
                389 => 1,
                390 => 1,
                397 => 1,
                419 => 1,
                420 => 1,
                465 => 1,
                472 => 1,
                473 => 1,
                496 => 1,
                524 => 1,
                544 => 1,
                545 => 1,
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
        return array();

    }//end getWarningList()


}//end class
