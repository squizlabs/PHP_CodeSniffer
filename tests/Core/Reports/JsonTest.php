<?php
/**
 * Tests for the Json report of PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once dirname(__FILE__).'/AbstractTestCase.php';

/**
 * Tests for the Json report of PHP_CodeSniffer.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Core_Reports_JsonTest extends Core_Reports_AbstractTestCase
{


    /**
     * Tests standard generation.
     * 
     * @return void
     */
    public function testGenerate()
    {
        $fullReport = new PHP_CodeSniffer_Reports_Json();
        $generated  = $this->getFixtureReport($fullReport);
        $expected   = '{"totals":{"warnings":2,"errors":8}';
        $this->assertEquals($expected, substr($generated, 0, strlen($expected)));

    }//end testGenerate()


}//end class

?>
