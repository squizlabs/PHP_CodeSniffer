<?php
/**
 * Unit test class for the DuplicateClassName multi-file sniff.
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
 * Unit test class for the DuplicateClassName multi-file sniff.
 *
 * A multi-file sniff unit test checks a .1.inc and a .2.inc file for expected violations
 * of a single coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Tests_Classes_DuplicateClassNameUnitTest extends AbstractSniffUnitTest
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
    public function getWarningList($testFile='')
    {
        switch ($testFile) {
        case 'DuplicateClassNameUnitTest.1.inc':
            return array(
                    6 => 1,
                    7 => 1,
                   );
            break;
        case 'DuplicateClassNameUnitTest.2.inc':
            return array(
                    2 => 1,
                    3 => 1,
                   );
            break;
        case 'DuplicateClassNameUnitTest.5.inc':
            return array(
                    3 => 1,
                    7 => 1,
                   );
            break;
        case 'DuplicateClassNameUnitTest.6.inc':
            return array(
                    10 => 1,
                   );
            break;
        default:
            return array();
            break;
        }//end switch

    }//end getWarningList()


}//end class

?>
