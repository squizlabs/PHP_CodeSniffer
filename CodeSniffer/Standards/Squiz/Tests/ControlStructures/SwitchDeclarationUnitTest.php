<?php
/**
 * Unit test class for the SwitchDeclaration sniff.
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
 * Unit test class for the SwitchDeclaration sniff.
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
class Squiz_Tests_ControlStructures_SwitchDeclarationUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='SwitchDeclarationUnitTest.inc')
    {
        return array(
                27  => 1,
                29  => 1,
                34  => 1,
                36  => 1,
                44  => 1,
                48  => 1,
                52  => 1,
                54  => 1,
                55  => 1,
                56  => 1,
                58  => 1,
                59  => 1,
                61  => 1,
                62  => 1,
                79  => 1,
                85  => 2,
                88  => 2,
                89  => 2,
                92  => 1,
                95  => 3,
                99  => 1,
                116 => 1,
                122 => 1,
                127 => 2,
                134 => 2,
                135 => 1,
                138 => 1,
                143 => 1,
                144 => 1,
                147 => 1,
                165 => 1,
                172 => 1,
                176 => 2,
                180 => 1,
                192 => 2,
                196 => 1,
                223 => 1,
                266 => 1,
               );

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
