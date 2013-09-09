<?php
/**
 * Tests for the JUnit report.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Oleg Lobach <oleg@lobach.info>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once dirname(__FILE__).'/AbstractTestCase.php';

/**
 * Tests for the Checkstyle report.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Oleg Lobach <oleg@lobach.info>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Core_Reports_JunitTest extends Core_Reports_AbstractTestCase
{


    /**
     * Tests standard report.
     *
     * @return void
     */
    public function testGenerate()
    {
        $junitReport = new PHP_CodeSniffer_Reports_Junit();
        $generated   = $this->getFixtureReport($junitReport);
        $xmlDocument = new DOMDocument();

        $xmlDocument->loadXML($generated);
        $result = $xmlDocument->schemaValidate(
            dirname(__FILE__).'/XSD/Junit.xsd'
        );

        $this->assertTrue($result);

    }//end testGenerate()


}//end class

?>
