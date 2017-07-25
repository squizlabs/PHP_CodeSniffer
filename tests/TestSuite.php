<?php
/**
 * A PHP_CodeSniffer specific test suite for PHPUnit.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests;

use PHPUnit\Framework\TestSuite as PHPUnit_TestSuite;
use PHPUnit\Framework\TestResult;

class TestSuite extends PHPUnit_TestSuite
{


    /**
     * Runs the tests and collects their result in a TestResult.
     *
     * @param \PHPUnit\Framework\TestResult $result A test result.
     * @param mixed                         $filter The filter passed to each test.
     *
     * @return \PHPUnit\Framework\TestResult
     */
    public function run(TestResult $result=null, $filter=false)
    {
        $result = parent::run($result, $filter);

        $codes = count($GLOBALS['PHP_CODESNIFFER_SNIFF_CODES']);

        echo PHP_EOL.PHP_EOL;
        echo "Tests generated $codes unique error codes";
        if ($codes > 0) {
            $fixes   = count($GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES']);
            $percent = round(($fixes / $codes * 100), 2);
            echo "; $fixes were fixable ($percent%)";
        }

        return $result;

    }//end run()


}//end class
