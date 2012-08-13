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
class Core_Reports_SourceTest extends Core_Reports_AbstractTestCase
{


    /**
     * Test standard generation.
     *
     * @return void
     */
    public function testGenerate()
    {
        $fullReport = new PHP_CodeSniffer_Reports_Source();
        $generated  = $this->getFixtureReport($fullReport);
        $this->assertContains(
            'A TOTAL OF 10 SNIFF VIOLATION(S) WERE FOUND IN 5 SOURCE(S)',
            $generated
        );

    }//end testGenerate()


}//end class

?>
