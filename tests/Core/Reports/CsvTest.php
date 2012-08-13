<?php
/**
 * Tests for the Csv report of PHP_CodeSniffer.
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
 * Tests for the Csv report of PHP_CodeSniffer.
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
class Core_Reports_CsvTest extends Core_Reports_AbstractTestCase
{

    /**
     * Path to generated files.
     *
     * @var string
     */
    protected $genFilesFolder;


    /**
     * Store path to generated files.
     * 
     * @return void
     */
    public function setUp()
    {
        $this->genFilesFolder = sys_get_temp_dir();
        parent::setUp();

    }//end setUp()


    /**
     * Tests standard report.
     * 
     * @return void
     */
    public function testGenerate()
    {
        $fullReport = new PHP_CodeSniffer_Reports_Csv();
        $generated  = $this->getFixtureReport($fullReport);

        $reportFile = $this->genFilesFolder.'/CsvReportResult.csv';
        file_put_contents($reportFile, $generated);
        $file = fopen($reportFile, 'r');
        while ($csvLine = fgetcsv($file)) {
            $this->assertEquals(7, count($csvLine));
        }

    }//end testGenerate()


}//end class

?>
