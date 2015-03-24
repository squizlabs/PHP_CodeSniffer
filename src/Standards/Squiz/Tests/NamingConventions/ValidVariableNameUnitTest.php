<?php
/**
 * Unit test class for the ValidVariableName sniff.
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
 * Unit test class for the ValidVariableName sniff.
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
class Squiz_Tests_NamingConventions_ValidVariableNameUnitTest extends AbstractSniffUnitTest
{


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
                   3   => 1,
                   5   => 1,
                   10  => 1,
                   12  => 1,
                   15  => 1,
                   17  => 1,
                   20  => 1,
                   22  => 1,
                   25  => 1,
                   27  => 1,
                   31  => 1,
                   33  => 1,
                   36  => 1,
                   37  => 1,
                   39  => 1,
                   42  => 1,
                   44  => 1,
                   53  => 1,
                   58  => 1,
                   62  => 1,
                   63  => 1,
                   64  => 1,
                   67  => 1,
                   81  => 1,
                   106 => 1,
                   107 => 1,
                   108 => 1,
                   117 => 1,
                  );

        // The trait test will only work in PHP versions where traits exist.
        if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            $errors[118] = 1;
        }

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
