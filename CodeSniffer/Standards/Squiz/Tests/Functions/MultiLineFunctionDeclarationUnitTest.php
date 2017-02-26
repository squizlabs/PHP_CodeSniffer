<?php
/**
 * Unit test class for the MultiLineFunctionDeclaration sniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Unit test class for the MultiLineFunctionDeclaration sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Tests_Functions_MultiLineFunctionDeclarationUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='MultiLineFunctionDeclarationUnitTest.inc')
    {
        if ($testFile === 'MultiLineFunctionDeclarationUnitTest.inc') {
            $errors = array(
                       2   => 1,
                       3   => 1,
                       4   => 2,
                       5   => 1,
                       7   => 1,
                       11  => 1,
                       12  => 1,
                       13  => 1,
                       16  => 1,
                       36  => 1,
                       43  => 2,
                       48  => 1,
                       81  => 1,
                       82  => 2,
                       88  => 1,
                       102 => 2,
                       137 => 1,
                       141 => 2,
                       142 => 1,
                       158 => 1,
                       160 => 1,
                      );
        } else {
            $errors = array(
                       2  => 1,
                       3  => 1,
                       4  => 2,
                       5  => 1,
                       7  => 1,
                       11 => 1,
                       12 => 1,
                       13 => 1,
                       16 => 1,
                       26 => 1,
                       36 => 1,
                       43 => 2,
                       48 => 1,
                      );
        }//end if

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
