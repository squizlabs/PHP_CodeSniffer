<?php
/**
 * Unit test class for the CamelCapsFunctionName sniff.
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
 * Unit test class for the CamelCapsFunctionName sniff.
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
class Generic_Tests_NamingConventions_CamelCapsFunctionNameUnitTest extends AbstractSniffUnitTest
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
                   10 => 1,
                   11 => 1,
                   12 => 1,
                   13 => 1,
                   16 => 1,
                   17 => 1,
                   20 => 1,
                   21 => 1,
                   24 => 1,
                   25 => 1,
                   30 => 1,
                   31 => 1,
                   50 => 1,
                   52 => 1,
                   53 => 1,
                   57 => 1,
                   58 => 1,
                   59 => 1,
                   60 => 1,
                   61 => 1,
                   62 => 1,
                   63 => 1,
                   64 => 1,
                   65 => 1,
                   66 => 1,
                   67 => 1,
                   68 => 1,
                   69 => 1,
                   71 => 1,
                   72 => 1,
                   73 => 1,
                   74 => 1,
                  );

        // The trait tests will only work in PHP version where traits exist and
        // will throw errors in earlier versions.
        if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            $errors[95] = 1;
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

?>
