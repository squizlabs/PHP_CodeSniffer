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
 * @copyright 2009-2014 SQLI <www.sqli.com>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Tests for the PHP_CodeSniffer_File:getMethodParameters method.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Anti Veeranna <duke@masendav.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009-2014 SQLI <www.sqli.com>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
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


    /**
     * Verify type hint parsing.
     *
     * @return void
     */
    public function testTypeHint()
    {
        $expected    = array();
        $expected[0] = array(
                        'name'              => '$var1',
                        'pass_by_reference' => false,
                        'type_hint'         => 'foo',
                       );

        $expected[1] = array(
                        'name'              => '$var2',
                        'pass_by_reference' => false,
                        'type_hint'         => 'bar',
                       );

        $start    = ($this->_phpcsFile->numTokens - 1);
        $function = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testTypeHint */'
        );

        $found = $this->_phpcsFile->getMethodParameters(($function + 2));
        $this->assertSame($expected, $found);

    }//end testTypeHint()


    /**
     * Verify variable.
     *
     * @return void
     */
    public function testVariable()
    {
        $expected    = array();
        $expected[0] = array(
                        'name'              => '$var',
                        'pass_by_reference' => false,
                        'type_hint'         => '',
                       );

        $start    = ($this->_phpcsFile->numTokens - 1);
        $function = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testVariable */'
        );

        $found = $this->_phpcsFile->getMethodParameters(($function + 2));
        $this->assertSame($expected, $found);

    }//end testVariable()


    /**
     * Verify default value parsing with a single function param.
     *
     * @return void
     */
    public function testSingleDefaultValue()
    {
        $expected    = array();
        $expected[0] = array(
                        'name'              => '$var1',
                        'default'           => 'self::CONSTANT',
                        'pass_by_reference' => false,
                        'type_hint'         => '',
                       );

        $start    = ($this->_phpcsFile->numTokens - 1);
        $function = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testSingleDefaultValue */'
        );

        $found = $this->_phpcsFile->getMethodParameters(($function + 2));
        $this->assertSame($expected, $found);

    }//end testSingleDefaultValue()


    /**
     * Verify default value parsing.
     *
     * @return void
     */
    public function testDefaultValues()
    {
        $expected    = array();
        $expected[0] = array(
                        'name'              => '$var1',
                        'default'           => '1',
                        'pass_by_reference' => false,
                        'type_hint'         => '',
                       );
        $expected[1] = array(
                        'name'              => '$var2',
                        'default'           => "'value'",
                        'pass_by_reference' => false,
                        'type_hint'         => '',
                       );

        $start    = ($this->_phpcsFile->numTokens - 1);
        $function = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testDefaultValues */'
        );

        $found = $this->_phpcsFile->getMethodParameters(($function + 2));
        $this->assertSame($expected, $found);

    }//end testDefaultValues()


}//end class

// @codingStandardsIgnoreStart
/* testPassByReference */ function passByReference(&$var) {}
/* testArrayHint */ function arrayHint(array $var) {}
/* testVariable */ function variable($var) {}
/* testSingleDefaultValue */ function defaultValue($var1=self::CONSTANT) {}
/* testDefaultValues */ function defaultValues($var1=1, $var2='value') {}
/* testTypeHint */ function typeHint(foo $var1, bar $var2) {}
// @codingStandardsIgnoreEnd

?>

