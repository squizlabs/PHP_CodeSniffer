<?php
/**
 * Tests for the Summary report.
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

/**
 * Tests for the Summary report of PHP_CodeSniffer.
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
class Core_Reports_SummaryTest extends Core_Reports_AbstractTestCase
{


    /**
     * Test standard generation.
     * 
     * @return void
     */
    public function testGenerate()
    {
        $fullReport = new PHP_CodeSniffer_Reports_Summary();
        $generated  = $this->getFixtureReport($fullReport);

        $i    = 1;
        $line = strtok($generated, PHP_EOL);
        while (false !== $line) {
            $this->assertLessThan(
                81,
                strlen($line),
                'Report line '.$i.' is longer than 80 characters'
            );
            $i++;
            $line = strtok(PHP_EOL);
        }

    }//end testGenerate()


}//end class

?>
