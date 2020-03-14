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
