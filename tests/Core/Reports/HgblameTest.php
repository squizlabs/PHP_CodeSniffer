<?php
/**
 * Tests for the Hgblame report of PHP_CodeSniffer.
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
require_once dirname(__FILE__).'/Mock/Hgblame.php';

if (is_file(dirname(__FILE__).'/../../../CodeSniffer.php') === true) {
    // We are not installed.
    include_once dirname(__FILE__).'/../../../CodeSniffer/Reports/VersionControl.php';
} else {
    include_once 'PHP/CodeSniffer/Reports/VersionControl.php';
}

/**
 * Tests for the Hgblame report of PHP_CodeSniffer.
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
class Core_Reports_HgblameTest extends Core_Reports_AbstractTestCase
{


    /**
     * Test standard generation
     *
     * @return void
     */
    public function testGenerate()
    {
        $fullReport     = new PHP_CodeSniffer_Reports_Mock_Hgblame();
        $generated      = $this->getFixtureReport($fullReport);
        $generatedLines = explode(PHP_EOL, $generated);
        $this->assertGreaterThan(10, count($generatedLines));

    }//end testGenerate()


    /**
     * Test author recovering from a hg blame line
     *
     * @param string $line     The hg blame output
     * @param string $expected The author name
     *
     * @dataProvider provideDataForGetHgAuthor
     *
     * @return void
     */
    public function testGetHgAuthor($line, $expected)
    {
        $fullReport = new PHP_CodeSniffer_Reports_Mock_Hgblame();
        $author     = $fullReport->testGetHgAuthor($line);
        $this->assertEquals($expected, $author);

    }//end testGetHgAuthor()


    /**
     * Data provider for testGetHgAuthor
     *
     * @return array
     */
    public static function provideDataForGetHgAuthor()
    {
        return array(
            array('Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**', 'Ben Selby'),
            array('    benmatselby@somewhere Sun May 29 00:05:15 2011 +0300:     /**', 'benmatselby@somewhere'),
            array('Ben Selby <benmatselby@gmail.com> Tue Apr 26 00:36:36 2011 +0300:  * // Some random text with dates (e.g. 2011-05-01 12:30:00, Y-m-d H:i:s', 'Ben Selby'),
        );

    }//end provideDataForGetHgAuthor()


}//end class

?>
