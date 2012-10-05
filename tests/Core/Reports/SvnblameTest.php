<?php
/**
 * Tests for the Source report of PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once dirname(__FILE__).'/AbstractTestCase.php';
require_once dirname(__FILE__).'/Mock/Svnblame.php';

if (is_file(dirname(__FILE__).'/../../../CodeSniffer.php') === true) {
    // We are not installed.
    include_once dirname(__FILE__).'/../../../CodeSniffer/Reports/VersionControl.php';
} else {
    include_once 'PHP/CodeSniffer/Reports/VersionControl.php';
}

/**
 * Tests for the Source report of PHP_CodeSniffer.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Core_Reports_SvnblameTest extends Core_Reports_AbstractTestCase
{


    /**
     * Test standard generation
     *
     * @return void
     */
    public function testGenerate()
    {
        $fullReport     = new PHP_CodeSniffer_Reports_Mock_Svnblame();
        $generated      = $this->getFixtureReport($fullReport);
        $generatedLines = explode(PHP_EOL, $generated);
        $this->assertGreaterThan(10, count($generatedLines));

    }//end testGenerate()


    /**
     * Test author recovering from an svn blame line
     *
     * @return void
     */
    public function testGetSvnAuthor()
    {
        $fixtureNormalRevisionNumber = '   123   devel1        * @return void';
        $fixtureBigRevisionNumber    = '123456   devel1        * @return void';

        $fullReport = new PHP_CodeSniffer_Reports_Mock_Svnblame();
        $author     = $fullReport->testGetSvnAuthor($fixtureNormalRevisionNumber);
        $this->assertEquals('devel1', $author);

        $author = $fullReport->testGetSvnAuthor($fixtureBigRevisionNumber);
        $this->assertEquals('devel1', $author);

    }//end testGetSvnAuthor()


}//end class

?>
