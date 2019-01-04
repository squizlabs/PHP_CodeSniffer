<?php
/**
 * Bootstrap file for PHP_CodeSniffer unit tests.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2017 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

if (defined('PHP_CODESNIFFER_IN_TESTS') === false) {
    define('PHP_CODESNIFFER_IN_TESTS', true);
}

if (defined('PHP_CODESNIFFER_CBF') === false) {
    define('PHP_CODESNIFFER_CBF', false);
}

if (defined('PHP_CODESNIFFER_VERBOSITY') === false) {
    define('PHP_CODESNIFFER_VERBOSITY', 0);
}

if (is_file(__DIR__.'/../autoload.php') === true) {
    include_once __DIR__.'/../autoload.php';
} else {
    include_once 'PHP/CodeSniffer/autoload.php';
}

$tokens = new \PHP_CodeSniffer\Util\Tokens();

// Compatibility for PHPUnit < 6 and PHPUnit 6+.
if (class_exists('PHPUnit_Framework_TestSuite') === true && class_exists('PHPUnit\Framework\TestSuite') === false) {
    class_alias('PHPUnit_Framework_TestSuite', 'PHPUnit'.'\Framework\TestSuite');
}

if (class_exists('PHPUnit_Framework_TestCase') === true && class_exists('PHPUnit\Framework\TestCase') === false) {
    class_alias('PHPUnit_Framework_TestCase', 'PHPUnit'.'\Framework\TestCase');
}

if (class_exists('PHPUnit_TextUI_TestRunner') === true && class_exists('PHPUnit\TextUI\TestRunner') === false) {
    class_alias('PHPUnit_TextUI_TestRunner', 'PHPUnit'.'\TextUI\TestRunner');
}

if (class_exists('PHPUnit_Framework_TestResult') === true && class_exists('PHPUnit\Framework\TestResult') === false) {
    class_alias('PHPUnit_Framework_TestResult', 'PHPUnit'.'\Framework\TestResult');
}


/**
 * A global util function to help print unit test fixing data.
 *
 * @return void
 */
function printPHPCodeSnifferTestOutput()
{
    echo PHP_EOL.PHP_EOL;

    $output = 'The test files';
    $data   = [];

    $codeCount = count($GLOBALS['PHP_CODESNIFFER_SNIFF_CODES']);
    if (empty($GLOBALS['PHP_CODESNIFFER_SNIFF_CASE_FILES']) === false) {
        $files     = call_user_func_array('array_merge', $GLOBALS['PHP_CODESNIFFER_SNIFF_CASE_FILES']);
        $files     = array_unique($files);
        $fileCount = count($files);

        $output = '%d sniff test files';
        $data[] = $fileCount;
    }

    $output .= ' generated %d unique error codes';
    $data[]  = $codeCount;

    if ($codeCount > 0) {
        $fixes   = count($GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES']);
        $percent = round(($fixes / $codeCount * 100), 2);

        $output .= '; %d were fixable (%d%%)';
        $data[]  = $fixes;
        $data[]  = $percent;
    }

    vprintf($output, $data);

}//end printPHPCodeSnifferTestOutput()
