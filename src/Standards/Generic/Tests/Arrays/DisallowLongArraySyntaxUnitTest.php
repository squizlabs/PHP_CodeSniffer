<?php
/**
 * Unit test class for the DisallowLongArraySyntax sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Arrays;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class DisallowLongArraySyntaxUnitTest extends AbstractSniffUnitTest
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
        $testFiles = [$testFileBase.'1.inc'];

        // HHVM doesn't tokenize any of the file after a git
        // merge conflict, so only run this check on non-HHVM versions.
        if (defined('HHVM_VERSION') === false) {
            $testFiles[] = $testFileBase.'2.inc';
        }

        return $testFiles;

    }//end getTestFiles()


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
        case 'DisallowLongArraySyntaxUnitTest.1.inc':
            return [
                2  => 1,
                4  => 1,
                6  => 1,
                7  => 1,
                12 => 1,
                13 => 1,
            ];
        case 'DisallowLongArraySyntaxUnitTest.2.inc':
            return [
                2 => 1,
                9 => 1,
            ];
        default:
            return [];
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
        return [];

    }//end getWarningList()


}//end class
