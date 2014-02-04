<?php
/**
 * Unit test class for the ForLoopDeclaration sniff.
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
 * Unit test class for the ForEachLoopDeclaration sniff.
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
class Squiz_Tests_ControlStructures_ForLoopDeclarationUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array(int => int)
     */
    public function getErrorList($testFile='ForLoopDeclarationUnitTest.inc')
    {
        switch ($testFile) {
        case 'ForLoopDeclarationUnitTest.inc':
            return array(
                8  => 2,
                11 => 2,
                14 => 2,
                17 => 2,
                21 => 6,
                27 => 1,
                30 => 1,
                37 => 2,
                39 => 2,
               );
             break;
        case 'ForLoopDeclarationUnitTest.js':
            return array(
                6  => 2,
                9  => 2,
                12 => 2,
                15 => 2,
                19 => 6,
                33 => 1,
                36 => 1,
                43 => 2,
                45 => 2,
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
     * @return array(int => int)
     */
    public function getWarningList()
    {
        return array();

    }//end getWarningList()


}//end class

?>
