<?php
/**
 * Tests for the \PHP_CodeSniffer\Filters\Filter::accept method.
 *
 * @author    Willington Vega <wvega@wvega.com>
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Filters\Filter;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Filters\Filter;
use PHP_CodeSniffer\Ruleset;
use PHPUnit\Framework\TestCase;

class AcceptTest extends TestCase
{

    /**
     * The Config object.
     *
     * @var \PHP_CodeSniffer\Config
     */
    protected static $config;

    /**
     * The Ruleset object.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    protected static $ruleset;


    /**
     * Initialize the test.
     *
     * @return void
     */
    public function setUp()
    {
        if ($GLOBALS['PHP_CODESNIFFER_PEAR'] === true) {
            // PEAR installs test and sniff files into different locations
            // so these tests will not pass as they directly reference files
            // by relative location.
            $this->markTestSkipped('Test cannot run from a PEAR install');
        }

    }//end setUp()


    /**
     * Initialize the config and ruleset objects based on the `AcceptTest.xml` ruleset file.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        if ($GLOBALS['PHP_CODESNIFFER_PEAR'] === true) {
            // This test will be skipped.
            return;
        }

        $standard      = __DIR__.'/'.basename(__FILE__, '.php').'.xml';
        self::$config  = new Config(["--standard=$standard", "--ignore=*/somethingelse/*"]);
        self::$ruleset = new Ruleset(self::$config);

    }//end setUpBeforeClass()


    /**
     * Test filtering a file list for excluded paths.
     *
     * @param array $inputPaths     List of file paths to be filtered.
     * @param array $expectedOutput Expected filtering result.
     *
     * @dataProvider dataExcludePatterns
     *
     * @return void
     */
    public function testExcludePatterns($inputPaths, $expectedOutput)
    {
        $fakeDI   = new \RecursiveArrayIterator($inputPaths);
        $filter   = new Filter($fakeDI, '/', self::$config, self::$ruleset);
        $iterator = new \RecursiveIteratorIterator($filter);
        $files    = [];

        foreach ($iterator as $file) {
            $files[] = $file;
        }

        $this->assertEquals($expectedOutput, $files);

    }//end testExcludePatterns()


    /**
     * Data provider.
     *
     * @see testExcludePatterns
     *
     * @return array
     */
    public function dataExcludePatterns()
    {
        $testCases = [
            // Test top-level exclude patterns.
            [
                [
                    '/path/to/src/Main.php',
                    '/path/to/src/Something/Main.php',
                    '/path/to/src/Somethingelse/Main.php',
                    '/path/to/src/SomethingelseEvenLonger/Main.php',
                    '/path/to/src/Other/Main.php',
                ],
                [
                    '/path/to/src/Main.php',
                    '/path/to/src/SomethingelseEvenLonger/Main.php',
                ],
            ],

            // Test ignoring standard/sniff specific exclude patterns.
            [
                [
                    '/path/to/src/generic-project/Main.php',
                    '/path/to/src/generic/Main.php',
                    '/path/to/src/anything-generic/Main.php',
                ],
                [
                    '/path/to/src/generic-project/Main.php',
                    '/path/to/src/generic/Main.php',
                    '/path/to/src/anything-generic/Main.php',
                ],
            ],
        ];

        // Allow these tests to work on Windows as well.
        if (DIRECTORY_SEPARATOR === '\\') {
            foreach ($testCases as $key => $case) {
                foreach ($case as $nr => $param) {
                    foreach ($param as $file => $value) {
                        $testCases[$key][$nr][$file] = strtr($value, '/', '\\');
                    }
                }
            }
        }

        return $testCases;

    }//end dataExcludePatterns()


}//end class
