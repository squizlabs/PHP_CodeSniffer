<?php
/**
 * Tests for the PHP_CodeSniffer_File:getMethodParameters method.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Anti Veeranna <duke@masendav.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: IsCamelCapsTest.php 240585 2007-08-02 00:05:40Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Tests for the PHP_CodeSniffer_File:getMethodParameters method.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Anti Veeranna <duke@masendav.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Core_File_GetMethodParametersTest extends PHPUnit_Framework_TestCase
{

    /**
     * The PHP_CodeSniffer_File object containing parsed contents of this file.
     *
     * @var PHP_CodeSniffer_File
     */
    private $_phpcsFile;


    /**
     * Initialize & tokenize PHP_CodeSniffer_File with code from this file.
     *
     * Methods used for these tests can be found at the bottom of
     * this file.
     *
     * @return void
     */
    public function setUp()
    {
        $phpcs            = new PHP_CodeSniffer();
        $this->_phpcsFile = new PHP_CodeSniffer_File(
            __FILE__,
            array(),
            array(),
            array(),
            $phpcs
        );

        $contents = file_get_contents(__FILE__);
        $this->_phpcsFile->start($contents);

    }//end setUp()


    /**
     * Clean up after finished test.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->_phpcsFile);

    }//end tearDown()


    /**
     * Verify pass-by-reference parsing.
     *
     * @return void
     */
    public function testPassByReference()
    {
        $expected    = array();
        $expected[0] = array(
                        'name'              => '$var',
                        'pass_by_reference' => true,
                        'type_hint'         => '',
                       );

        $start    = ($this->_phpcsFile->numTokens - 1);
        $function = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testPassByReference */'
        );

        $found = $this->_phpcsFile->getMethodParameters(($function + 2));
        $this->assertSame($expected, $found);

    }//end testPassByReference()


    /**
     * Verify array hint parsing.
     *
     * @return void
     */
    public function testArrayHint()
    {

        $expected    = array();
        $expected[0] = array(
                        'name'              => '$var',
                        'pass_by_reference' => false,
                        'type_hint'         => 'array',
                       );

        $start    = ($this->_phpcsFile->numTokens - 1);
        $function = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testArrayHint */'
        );

        $found = $this->_phpcsFile->getMethodParameters(($function + 2));
        $this->assertSame($expected, $found);

    }//end testArrayHint()


}//end class

// @codingStandardsIgnoreStart
/* testPassByReference */ function passByReference(&$var) {}
/* testArrayHint */ function arrayHint(array $var) {}
// @codingStandardsIgnoreEnd

?>

