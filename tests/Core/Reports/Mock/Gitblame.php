<?php
/**
 * Gitblame report mock class.
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

if (is_file(dirname(__FILE__).'/../../../../CodeSniffer.php') === true) {
    // We are not installed.
    include_once dirname(__FILE__).'/../../../../CodeSniffer/Report.php';
    include_once dirname(__FILE__).'/../../../../CodeSniffer/Reports/VersionControl.php';
    include_once dirname(__FILE__).'/../../../../CodeSniffer/Reports/Gitblame.php';
} else {
    include_once 'PHP/CodeSniffer/Report.php';
    include_once 'PHP/CodeSniffer/Reports/VersionControl.php';
    include_once 'PHP/CodeSniffer/Reports/Gitblame.php';
}

/**
 * Gitblame report mock class.
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
class PHP_CodeSniffer_Reports_Mock_Gitblame extends PHP_CodeSniffer_Reports_Gitblame
{

    /**
     * Example gitblame output.
     *
     * @var array
     */
    protected $fooBlames = array(
        '054e7580 (Ben Selby 1 2009-08-25  45)      * @return',
        '054e758a (Ben Selby 2 2009-08-25  45)      * @return',
        '054e758b (Ben Selby 3 2009-08-25  45)      * @return',
        '054e758c (Ben Selby 4 2009-08-25  45)      * @return',
        '054e758d (Ben Selby 5 2009-08-25  45)      * @return',
        '1ee0f411 (Ben Selby 6 2009-08-25  45)      * @return',
        '1ee0f41b (Ben Selby 7 2009-08-25  45)      * @return',
        '1ee0f41c (Ben Selby 8 2009-08-25  45)      * @return',
        '1ee0f41d (Ben Selby 9 2009-08-25  45)      * @return',
        '1ee0f41e (Ben Selby 10 2009-08-25  45)      * @return',
    );

    /**
     * Example gitblame output.
     *
     * @var array
     */
    protected $barBlames = array(
        '054e7580 (Ben Selby 1 2009-08-25  45)      * @return',
        '054e758a (Ben Selby 2 2009-08-25  45)      * @return',
        '054e758b (Ben Selby 3 2009-08-25  45)      * @return',
        '054e758c (Ben Selby 4 2009-08-25  45)      * @return',
        '054e758d (Ben Selby 5 2009-08-25  45)      * @return',
        '1ee0f411 (Ben Selby 6 2009-08-25  45)      * @return',
        '1ee0f41b (Ben Selby 7 2009-08-25  45)      * @return',
        '1ee0f41c (Ben Selby 8 2009-08-25  45)      * @return',
        '1ee0f41d (Ben Selby 9 2009-08-25  45)      * @return',
        '1ee0f41e (Ben Selby 10 2009-08-25  45)      * @return',
    );

    /**
     * Example gitblame output.
     *
     * @var array
     */
    protected $bazBlames = array(
        '054e7580 (Ben Selby 1 2009-08-25  45)      * @return',
        '054e758a (Ben Selby 2 2009-08-25  45)      * @return',
        '054e758b (Ben Selby 3 2009-08-25  45)      * @return',
        '054e758c (Ben Selby 4 2009-08-25  45)      * @return',
        '054e758d (Ben Selby 5 2009-08-25  45)      * @return',
        '1ee0f411 (Ben Selby 6 2009-08-25  45)      * @return',
        '1ee0f41b (Ben Selby 7 2009-08-25  45)      * @return',
        '1ee0f41c (Ben Selby 8 2009-08-25  45)      * @return',
        '1ee0f41d (Ben Selby 9 2009-08-25  45)      * @return',
        '1ee0f41e (Ben Selby 10 2009-08-25  45)      * @return',
    );

    /**
     * Example gitblame output with long revision numbers.
     *
     * @var array
     */
    protected $bigRevisionNumberBlames = array(
        '054e7580 (Ben Selby 1 2009-08-25  45)      * @return',
        '054e758a (Ben Selby 2 2009-08-25  45)      * @return',
        '054e758b (Ben Selby 3 2009-08-25  45)      * @return',
        '054e758c (Ben Selby 4 2009-08-25  45)      * @return',
        '054e758d (Ben Selby 5 2009-08-25  45)      * @return',
        '1ee0f411 (Ben Selby 6 2009-08-25  45)      * @return',
        '1ee0f41b (Ben Selby 7 2009-08-25  45)      * @return',
        '1ee0f41c (Ben Selby 8 2009-08-25  45)      * @return',
        '1ee0f41d (Ben Selby 9 2009-08-25  45)      * @return',
        '1ee0f41e (Ben Selby 10 2009-08-25  45)      * @return',
    );


    /**
     * Mocks the gitblame command.
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
        }

        return $blames;

    }//end getGitblameContent()


    /**
     * Needed to test protected method.
     *
     * @param string $line Line to parse.
     *
     * @return string
     */
    public function testGetGitAuthor($line)
    {
        return $this->getAuthor($line);

    }//end testGetGitAuthor()


}//end class

?>
