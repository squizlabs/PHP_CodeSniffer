<?php
/**
 * Hgblame report mock class.
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
    include_once dirname(__FILE__).'/../../../../CodeSniffer/Reports/Hgblame.php';
} else {
    include_once 'PHP/CodeSniffer/Report.php';
    include_once 'PHP/CodeSniffer/Reports/VersionControl.php';
    include_once 'PHP/CodeSniffer/Reports/Hgblame.php';
}

/**
 * Hgblame report mock class.
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
class PHP_CodeSniffer_Reports_Mock_Hgblame extends PHP_CodeSniffer_Reports_Hgblame
{

    /**
     * Example Hgblame output.
     *
     * @var array
     */
    protected $fooBlames = array(
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
    );

    /**
     * Example Hgblame output.
     *
     * @var array
     */
    protected $barBlames = array(
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
    );

    /**
     * Example Hgblame output.
     *
     * @var array
     */
    protected $bazBlames = array(
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
    );

    /**
     * Example Hgblame output with long revision numbers.
     *
     * @var array
     */
    protected $bigRevisionNumberBlames = array(
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
        'Ben Selby <benmatselby@gmail.com> Sun May 29 00:05:15 2011 +0300:     /**',
    );


    /**
     * Mocks the Hgblame command.
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

    }//end getHgblameContent()


    /**
     * Needed to test protected method.
     *
     * @param string $line Line to parse.
     *
     * @return string
     */
    public function testGetHgAuthor($line)
    {
        return $this->getAuthor($line);

    }//end testGetHgAuthor()


}//end class

?>
