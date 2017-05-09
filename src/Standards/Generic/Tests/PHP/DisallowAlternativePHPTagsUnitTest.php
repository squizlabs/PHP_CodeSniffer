<?php
/**
 * Unit test class for the DisallowAlternativePHPTags sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class DisallowAlternativePHPTagsUnitTest extends AbstractSniffUnitTest
{


    /**
     * Get a list of all test files to check.
     *
     * @param string $testFileBase The base path that the unit tests files will have.
     *
     * @return string[]
     */
    protected function getTestFiles($testFileBase)
    {
        $testFiles = array($testFileBase.'1.inc');

        $aspTags = false;
        if (PHP_VERSION_ID < 70000) {
            $aspTags = (boolean) ini_get('asp_tags');
        }

        if ($aspTags === true) {
            $testFiles[] = $testFileBase.'2.inc';
        }

        return $testFiles;

    }//end getTestFiles()


    /**
     * Skip this test on HHVM.
     *
     * @return bool Whether to skip this test.
     */
    protected function shouldSkipTest()
    {
        return defined('HHVM_VERSION');

    }//end shouldSkipTest()


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
    public function getErrorList($testFile='')
    {
        switch ($testFile) {
        case 'DisallowAlternativePHPTagsUnitTest.1.inc':
            return array(
                    4  => 1,
                    7  => 1,
                    8  => 1,
                    11 => 1,
                   );
        case 'DisallowAlternativePHPTagsUnitTest.2.inc':
            return array(
                    2 => 1,
                    3 => 1,
                    4 => 1,
                    5 => 1,
                   );
        default:
            return array();
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
