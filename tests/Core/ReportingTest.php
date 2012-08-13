<?php
/**
 * Tests for the PHP_CodeSniffer_Reporting class.
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

/**
 * Tests for the PHP_CodeSniffer reporting system.
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
class Core_ReportingTest extends PHPUnit_Framework_TestCase
{

    /**
     * Called class
     *
     * @var PHP_CodeSniffer_Reporting
     */
    protected $reporting;

    /**
     * Report error fixtures.
     * 
     * @var array
     */
    protected $fixtureErrors = array(
                                10 => array(
                                       1 => array(
                                             array(
                                              'message'  => 'Fourth error message',
                                              'source'   => 'MyStandard.MyType.Mysniff1.Mycode1',
                                              'severity' => 5,
                                             ),
                                            )
                                      ),
                                1  => array(
                                       10 => array(
                                              array(
                                               'message'  => 'Second error message',
                                               'source'   => 'MyStandard.MyType.Mysniff2.Mycode1',
                                               'severity' => 5,
                                              ),
                                              array(
                                               'message'  => 'Third error message',
                                               'source'   => 'MyStandard.MyType.Mysniff1.Mycode2',
                                               'severity' => 5,
                                              ),
                                             ),
                                       1  => array(
                                              array(
                                               'message'  => 'First error message',
                                               'source'   => 'MyStandard.MyType.Mysniff1.Mycode2',
                                               'severity' => 5,
                                              ),
                                             )
                                      )
                               );

    /**
     * Report warning fixtures.
     * 
     * @var array
     */
    protected $fixtureWarnings = array(
                                  10 => array(
                                         1 => array(
                                               array(
                                                'message'  => 'First warning message',
                                                'source'   => 'MyStandard.MyType.Mysniff1.Mycode3',
                                                'severity' => 5,
                                               ),
                                              )
                                        ),
                                  5  => array(
                                         1 => array(
                                               array(
                                                'message'  => 'Second warning message',
                                                'source'   => 'MyStandard.MyType.Mysniff2.Mycode2',
                                                'severity' => 5,
                                               ),
                                              )
                                        ),
                                 );


    /**
     * Gives a Reporting instance.
     * 
     * @return void
     */
    public function setUp()
    {
        $this->reporting = new PHP_CodeSniffer_Reporting();

    }//end setUp()


    /**
     * Test report factory method.
     *
     * @return void
     */
    public function testFactory()
    {
        $type        = 'checkstyle';
        $reportClass = $this->reporting->factory($type);
        $this->assertInstanceOf('PHP_CodeSniffer_Report', $reportClass);
        $this->assertInstanceOf('PHP_CodeSniffer_Reports_Checkstyle', $reportClass);

        $this->setExpectedException('PHP_CodeSniffer_Exception');
        $type        = 'foo';
        $reportClass = $this->reporting->factory($type);

    }//end testFactory()


    /**
     * Compose fixture violations.
     * 
     * @return array
     */
    protected function getFixtureFilesViolations()
    {
        return array(
                'foo' => array(
                          'errors'      => $this->fixtureErrors,
                          'warnings'    => $this->fixtureWarnings,
                          'numErrors'   => 4,
                          'numWarnings' => 2,
                         ),
                'bar' => array(
                          'errors'      => $this->fixtureErrors,
                          'warnings'    => array(),
                          'numErrors'   => 4,
                          'numWarnings' => 0,
                         ),
                'baz' => array(
                          'errors'      => array(),
                          'warnings'    => array(),
                          'numErrors'   => 0,
                          'numWarnings' => 0,
                         )
               );

    }//end getFixtureFilesViolations()


    /**
     * Test prepare report method.
     * 
     * @return void
     */
    public function testPrepare()
    {
        $fixtureFilesViolations = $this->getFixtureFilesViolations();
        $reports = $this->reporting->prepare($fixtureFilesViolations);

        $this->assertArrayHasKey('files', $reports);
        $this->assertEquals(3, count($reports['files']));

        $this->assertArrayHasKey('foo', $reports['files']);
        $this->assertArrayHasKey('errors', $reports['files']['foo']);
        $this->assertEquals(4, $reports['files']['foo']['errors']);
        $this->assertArrayHasKey('warnings', $reports['files']['foo']);
        $this->assertEquals(2, $reports['files']['foo']['warnings']);

        // Two errors on line 1 column 10.
        $this->assertArrayHasKey('messages', $reports['files']['foo']);
        $fooMessages = $reports['files']['foo']['messages'];

        $this->assertArrayHasKey(1, $fooMessages, 'messages on line 1');
        $this->assertArrayHasKey(10, $fooMessages[1], 'messages on line 1 column 10');
        $this->assertEquals(2, count($fooMessages[1][10]), '2 messages on line 1 column 10');

        // One error one warning on line 10 column 1.
        $this->assertArrayHasKey(10, $fooMessages, 'messages on line 10');
        $this->assertArrayHasKey(1, $fooMessages[10], 'messages on line 10 column 1');
        $this->assertEquals(2, count($fooMessages[10][1]), '2 messages on line 10 column 1');

        // Empty file has structure without data.
        $this->assertArrayHasKey('baz', $reports['files']);
        $this->assertArrayHasKey('messages', $reports['files']['baz']);
        $this->assertEquals(0, count($reports['files']['baz']['messages']));

        // Totals.
        $this->assertArrayHasKey('totals', $reports, 'report totals exist');
        $this->assertArrayHasKey('errors', $reports['totals'], 'errors total exists');
        $this->assertEquals(8, $reports['totals']['errors'], 'errors total is well calculated');
        $this->assertArrayHasKey('warnings', $reports['totals'], 'warnings total exists');
        $this->assertEquals(2, $reports['totals']['warnings'], 'warnings total is well calculated');

        // Files Order.
        reset($reports['files']);
        $this->assertEquals('bar', key($reports['files']), 'report files ordered by name');
        next($reports['files']);
        $this->assertEquals('baz', key($reports['files']), 'report files ordered by name');

        // Violations Order.
        reset($fooMessages);
        $this->assertEquals(1, key($fooMessages), 'line level violations order');
        next($fooMessages);
        $this->assertEquals(5, key($fooMessages), 'line level violations order');
        reset($fooMessages[1]);
        $this->assertEquals(1, key($fooMessages[1]), 'column level violations order');
        next($fooMessages[1]);
        $this->assertEquals(10, key($fooMessages[1]), 'column level violations order');

    }//end testPrepare()


}//end class

?>
