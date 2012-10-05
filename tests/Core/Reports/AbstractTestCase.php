<?php
/**
 * TestCase Abstract Helper class.
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
 * TestCase Abstract Helper class.
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
class Core_Reports_AbstractTestCase extends PHPUnit_Framework_TestCase
{

    /**
     * Fixtures of report data.
     * 
     * @var array
     */
    protected $fixtureReportData
        = array(
           'totals' => array(
                        'warnings' => 2,
                        'errors'   => 8,
                       ),
           'files'  => array(
                        'bar' => array(
                                  'errors'   => 4,
                                  'warnings' => 0,
                                  'messages' => array(
                                                 1  => array(
                                                        1  => array(
                                                               0 => array(
                                                                     'message'  => 'First error message',
                                                                     'source'   => 'MyStandard.Mytype.Mysniff1Sniff.Mycode2',
                                                                     'type'     => 'ERROR',
                                                                     'severity' => 5,
                                                                    ),
                                                              ),
                                                        10 => array(
                                                               0 => array(
                                                                     'message'  => 'Second error message',
                                                                     'source'   => 'MyStandard.Mytype.Mysniff2Sniff.Mycode1',
                                                                     'type'     => 'ERROR',
                                                                     'severity' => 5,
                                                                    ),
                                                               1 => array(
                                                                     'message'  => 'Third error message',
                                                                     'source'   => 'MyStandard.Mytype.Mysniff1Sniff.Mycode2',
                                                                     'type'     => 'ERROR',
                                                                     'severity' => 5,
                                                                    )
                                                              )
                                                       ),
                                                 10 => array(
                                                        1 => array(
                                                              0 => array(
                                                                    'message'  => 'Fourth error message',
                                                                    'source'   => 'MyStandard.Mytype.Mysniff1Sniff.Mycode1',
                                                                    'type'     => 'ERROR',
                                                                    'severity' => 5,
                                                                   )
                                                             )
                                                       )
                                                )
                                 ),
                        'baz' => array(
                                  'errors'   => 0,
                                  'warnings' => 0,
                                  'messages' => array(),
                                 ),
                        'foo' => array(
                                  'errors'   => 4,
                                  'warnings' => 2,
                                  'messages' => array(
                                                 1  => array(
                                                        1  => array(
                                                               0 => array(
                                                                     'message'  => 'First error message',
                                                                     'source'   => 'MyStandard.Mytype.Mysniff1Sniff.Mycode2',
                                                                     'type'     => 'ERROR',
                                                                     'severity' => 5,
                                                                    )
                                                              ),
                                                        10 => array(
                                                               0 => array(
                                                                     'message'  => 'Second error message',
                                                                     'source'   => 'MyStandard.Mytype.Mysniff2Sniff.Mycode1',
                                                                     'type'     => 'ERROR',
                                                                     'severity' => 5,
                                                                    ),
                                                               1 => array(
                                                                     'message'  => 'Third error message',
                                                                     'source'   => 'MyStandard.Mytype.Mysniff1Sniff.Mycode2',
                                                                     'type'     => 'ERROR',
                                                                     'severity' => 5,
                                                                    )
                                                              )
                                                       ),
                                                 5  => array(
                                                        1 => array(
                                                              0 => array(
                                                                    'message'  => 'First warning message',
                                                                    'source'   => 'MyStandard.Mytype.Mysniff2Sniff.Mycode2',
                                                                    'type'     => 'WARNING',
                                                                    'severity' => 5,
                                                                   )
                                                             )
                                                       ),
                                                 10 => array(
                                                        1 => array(
                                                              0 => array(
                                                                    'message'  => 'Second warning message',
                                                                    'source'   => 'MyStandard.Mytype.Mysniff1Sniff.Mycode3',
                                                                    'type'     => 'WARNING',
                                                                    'severity' => 5,
                                                                   ),
                                                              1 => array(
                                                                    'message'  => 'Fourth error message',
                                                                    'source'   => 'MyStandard.Mytype.Mysniff1Sniff.Mycode1',
                                                                    'type'     => 'ERROR',
                                                                    'severity' => 5,
                                                                   )
                                                             )
                                                       )
                                                )
                                 )
                       )
          );


    /**
     * Returns report standard generation.
     * 
     * @param PHP_CodeSniffer_Report $report The report under test.
     * 
     * @return string
     */
    protected function getFixtureReport(PHP_CodeSniffer_Report $report)
    {
        ob_start();
        $report->generate($this->fixtureReportData);
        $generated = ob_get_clean();

        return $generated;

    }//end getFixtureReport()


}//end class

?>
