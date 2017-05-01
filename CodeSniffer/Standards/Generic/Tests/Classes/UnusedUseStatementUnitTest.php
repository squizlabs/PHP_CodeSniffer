<?php
/**
 * Unit test class for the UnusedUseStatement sniff.
 *
 * PHP versions 5 and 7
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Klaus Purer <klaus.purer@gmail.com>
 * @author    Alex Pott <alexpott@157725.no-reply.drupal.org>
 * @copyright 2015-2016 Klaus Purer
 * @copyright 2015-2016 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
/**
 * Unit test class for the UnusedUseStatement sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Klaus Purer <klaus.purer@gmail.com>
 * @author    Alex Pott <alexpott@157725.no-reply.drupal.org>
 * @copyright 2015-2016 Klaus Purer
 * @copyright 2015-2016 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Tests_Classes_UnusedUseStatementUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getErrorList($testFile='')
    {
        switch ($testFile) {
            case 'UnusedUseStatementUnitTest.1.inc':
                return array(
                        5 => 1,
                        6 => 1,
                        7 => 1,
                        10 => 1,
                        11 => 1,
                        12 => 1,
                        14 => 1,
                        16 => 1,
                        17 => 1,
                        20 => 1,
                        26 => 1,
                        27 => 1,
                        28 => 1,
                        29 => 1,
                        30 => 2,
                        31 => 1,
                        32 => 1,
                        33 => 1,
                        34 => 1,
                        35 => 1,
                       );
            case 'UnusedUseStatementUnitTest.2.inc':
                return array(
                        5 => 1,
                        12 => 1,
                       );
            default:
                return array();
        }

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getWarningList($testFile='')
    {
        switch ($testFile) {
            case 'UnusedUseStatementUnitTest.1.inc':
                return array(
                    22 => 1,
                    23 => 1,
                    24 => 1,
                    25 => 1,
                   );

            case 'UnusedUseStatementUnitTest.2.inc':
                return array();
            default:
                return array();
        }

    }//end getWarningList()


}//end class
