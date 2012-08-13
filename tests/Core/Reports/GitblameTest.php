<?php
/**
 * Tests for the Gitblame report of PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Ben Selby <benmatselby@gmail.com>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once dirname(__FILE__).'/AbstractTestCase.php';
require_once dirname(__FILE__).'/Mock/Gitblame.php';

if (is_file(dirname(__FILE__).'/../../../CodeSniffer.php') === true) {
    // We are not installed.
    include_once dirname(__FILE__).'/../../../CodeSniffer/Reports/VersionControl.php';
} else {
    include_once 'PHP/CodeSniffer/Reports/VersionControl.php';
}

/**
 * Tests for the Gitblame report of PHP_CodeSniffer.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Ben Selby <benmatselby@gmail.com>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Core_Reports_GitblameTest extends Core_Reports_AbstractTestCase
{


    /**
     * Test standard generation
     *
     * @return void
     */
    public function testGenerate()
    {
        $fullReport     = new PHP_CodeSniffer_Reports_Mock_Gitblame();
        $generated      = $this->getFixtureReport($fullReport);
        $generatedLines = explode(PHP_EOL, $generated);
        $this->assertGreaterThan(10, count($generatedLines));

    }//end testGenerate()


    /**
     * Test author recovering from a git blame line
     *
     * @param string $line     The git blame output
     * @param string $expected The author name
     *
     * @dataProvider provideDataForGetGitAuthor
     *
     * @return void
     */
    public function testGetGitAuthor($line, $expected)
    {
        $fullReport = new PHP_CodeSniffer_Reports_Mock_gitblame();
        $author     = $fullReport->testGetGitAuthor($line);
        $this->assertEquals($expected, $author);

    }//end testGetGitAuthor()


    /**
     * Data provider for testGetGitAuthor
     *
     * @return array
     */
    public static function provideDataForGetGitAuthor()
    {
        return array(
            array('054e758d (Ben Selby 2010-07-03  45)      * @return', 'Ben Selby'),
            array('054e758d (Ben Selby Dev 1 2010-07-03  45)      * @return', 'Ben Selby Dev 1'),
            array('054e758d (Ben 2010-07-03  45)      * @return', 'Ben'),
            array('054e758d (Ben Selby 2010-07-03 45)      * @return', 'Ben Selby'),
            array('054e758d (Ben Selby 2010-07-03 1)      * @return', 'Ben Selby'),
            array('054e758d (Ben Selby 2010-07-03 11)      * @return', 'Ben Selby'),
            array('054e758d (Ben Selby 2010-07-03 111)      * @return', 'Ben Selby'),
            array('054e758d (Ben Selby 2010-07-03 1111)      * @return', 'Ben Selby'),
            array('054e758d (Ben Selby 2010-07-03 11111)      * @return', 'Ben Selby'),
        );

    }//end provideDataForGetGitAuthor()


}//end class

?>
