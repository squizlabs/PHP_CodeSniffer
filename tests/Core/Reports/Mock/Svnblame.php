<?php
/**
 * Svnblame report mock class.
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

if (is_file(dirname(__FILE__).'/../../../../CodeSniffer.php') === true) {
    // We are not installed.
    include_once dirname(__FILE__).'/../../../../CodeSniffer/Report.php';
    include_once dirname(__FILE__).'/../../../../CodeSniffer/Reports/VersionControl.php';
    include_once dirname(__FILE__).'/../../../../CodeSniffer/Reports/Svnblame.php';
} else {
    include_once 'PHP/CodeSniffer/Report.php';
    include_once 'PHP/CodeSniffer/Reports/VersionControl.php';
    include_once 'PHP/CodeSniffer/Reports/Svnblame.php';
}

/**
 * Svnblame report mock class.
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
class PHP_CodeSniffer_Reports_Mock_Svnblame extends PHP_CodeSniffer_Reports_Svnblame
{

    /**
     * Example svnblame output.
     *
     * @var array
     */
    protected $fooBlames = array(
                            '     4   devel1        * @return void',
                            '     4   devel1        * @return void',
                            '     5   devel2        * @return void',
                            '     4   devel1        * @return void',
                            '     4   devel1        * @return void',
                            '     5   devel2        * @return void',
                            '     5   devel2        * @return void',
                            '     4   devel1        * @return void',
                            '    10   devel3        * @return void',
                            '    10   devel3        * @return void',
                           );

    /**
     * Example svnblame output.
     *
     * @var array
     */
    protected $barBlames = array(
                            '     4   devel1        * @return void',
                            '     4   devel1        * @return void',
                            '     5   devel2        * @return void',
                            '     4   devel1        * @return void',
                            '     4   devel1        * @return void',
                            '     5   devel2        * @return void',
                            '     5   devel2        * @return void',
                            '     4   devel1        * @return void',
                            '    10   devel3        * @return void',
                            '    10   devel3        * @return void',
                           );

    /**
     * Example svnblame output.
     *
     * @var array
     */
    protected $bazBlames = array(
                            '     4   devel1        * @return void',
                            '     4   devel1        * @return void',
                            '     5   devel2        * @return void',
                            '     4   devel1        * @return void',
                            '     4   devel1        * @return void',
                            '     5   devel2        * @return void',
                            '     5   devel2        * @return void',
                            '     4   devel1        * @return void',
                            '    10   devel3        * @return void',
                            '    10   devel3        * @return void',
                           );

    /**
     * Example svnblame output with long revision numbers.
     *
     * @var array
     */
    protected $bigRevisionNumberBlames = array(
                                          '123456   devel1        * @return void',
                                          '123456   devel1        * @return void',
                                          '251897   devel3        * @return void',
                                          '251897   devel3        * @return void',
                                          ' 12345   devel1        * @return void',
                                          '220123   devel2        * @return void',
                                          '220123   devel2        * @return void',
                                          '220123   devel2        * @return void',
                                          '219571   devel1        * @return void',
                                          '219571   devel1        * @return void',
                                         );


    /**
     * Mocks the svnblame command.
     *
     * @param string $filename filename (equals fixtures keys).
     *
     * @return string
     * @throws PHP_CodeSniffer_Exception
     */
    protected function getBlameContent($filename)
    {
        switch ($filename) {
        case 'foo':
            $blames = $this->fooBlames;
            break;
        case 'bar':
            $blames = $this->barBlames;
            break;
        case 'baz':
            $blames = $this->bazBlames;
            break;
        case 'bigRevisionNumber':
            $blames = $this->bigRevisionNumberBlames;
            break;
        default:
            throw new PHP_CodeSniffer_Exception('Unexpected filename '.$filename);
        }//end switch

        return $blames;

    }//end getSvnblameContent()


    /**
     * Needed to test protected method.
     *
     * @param string $line Line to parse.
     *
     * @return string
     */
    public function testGetSvnAuthor($line)
    {
        return $this->getAuthor($line);

    }//end testGetSvnAuthor()


}//end class

?>
