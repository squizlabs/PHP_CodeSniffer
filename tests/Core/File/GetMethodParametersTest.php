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
 *
 * @group utilityMethods
 */
class Core_File_GetMethodParametersTest extends PHPUnit_Framework_TestCase
{

    /**
     * The PHP_CodeSniffer_File object containing parsed contents of the test case file.
     *
     * @var PHP_CodeSniffer_File
     */
    private $_phpcsFile;


    /**
     * Initialize & tokenize PHP_CodeSniffer_File with code from the test case file.
     *
     * Methods used for these tests can be found in a test case file in the same
     * directory and with the same name, using the .inc extension.
     *
     * @return void
     */
    public function setUp()
    {
        $pathToTestcases  = dirname(__FILE__).'/'.basename(__FILE__, '.php').'.inc';
        $phpcs            = new PHP_CodeSniffer();
        $this->_phpcsFile = new PHP_CodeSniffer_File(
            $pathToTestcases,
            array(),
            array(),
            $phpcs
        );

        $contents = file_get_contents($pathToTestcases);
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
                        'token'             => 9,
                        'name'              => '$var',
                        'content'           => '&$var',
                        'pass_by_reference' => true,
                        'variable_length'   => false,
                        'type_hint'         => '',
                        'nullable_type'     => false,
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
                        'token'             => 24,
                        'name'              => '$var',
                        'content'           => 'array $var',
                        'pass_by_reference' => false,
                        'variable_length'   => false,
                        'type_hint'         => 'array',
                        'nullable_type'     => false,
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
                        'token'             => 89,
                        'name'              => '$var1',
                        'content'           => 'foo $var1',
                        'pass_by_reference' => false,
                        'variable_length'   => false,
                        'type_hint'         => 'foo',
                        'nullable_type'     => false,
                       );

        $expected[1] = array(
                        'token'             => 94,
                        'name'              => '$var2',
                        'content'           => 'bar $var2',
                        'pass_by_reference' => false,
                        'variable_length'   => false,
                        'type_hint'         => 'bar',
                        'nullable_type'     => false,
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
     * Verify self type hint parsing.
     *
     * @return void
     */
    public function testSelfTypeHint()
    {
        $expected    = array();
        $expected[0] = array(
                        'token'             => 115,
                        'name'              => '$var',
                        'content'           => 'self $var',
                        'pass_by_reference' => false,
                        'variable_length'   => false,
                        'type_hint'         => 'self',
                        'nullable_type'     => false
                       );

        $start    = ($this->_phpcsFile->numTokens - 1);
        $function = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testSelfTypeHint */'
        );

        $found = $this->_phpcsFile->getMethodParameters(($function + 2));
        $this->assertSame($expected, $found);

    }//end testTypeHint()


    /**
     * Verify nullable type hint parsing.
     *
     * @return void
     */
    public function testNullableTypeHint()
    {
        $expected    = array();
        $expected[0] = array(
                        'token'             => 133,
                        'name'              => '$var1',
                        'content'           => '?int $var1',
                        'pass_by_reference' => false,
                        'variable_length'   => false,
                        'type_hint'         => '?int',
                        'nullable_type'     => true,
                       );

        $expected[1] = array(
                        'token'             => 140,
                        'name'              => '$var2',
                        'content'           => '?\bar $var2',
                        'pass_by_reference' => false,
                        'variable_length'   => false,
                        'type_hint'         => '?\bar',
                        'nullable_type'     => true,
                       );

        $start    = ($this->_phpcsFile->numTokens - 1);
        $function = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testNullableTypeHint */'
        );

        $found = $this->_phpcsFile->getMethodParameters(($function + 2));
        $this->assertSame($expected, $found);

    }//end testNullableTypeHint()


    /**
     * Verify variable.
     *
     * @return void
     */
    public function testVariable()
    {
        $expected    = array();
        $expected[0] = array(
                        'token'             => 37,
                        'name'              => '$var',
                        'content'           => '$var',
                        'pass_by_reference' => false,
                        'variable_length'   => false,
                        'type_hint'         => '',
                        'nullable_type'     => false,
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
                        'token'             => 50,
                        'name'              => '$var1',
                        'content'           => '$var1=self::CONSTANT',
                        'default'           => 'self::CONSTANT',
                        'pass_by_reference' => false,
                        'variable_length'   => false,
                        'type_hint'         => '',
                        'nullable_type'     => false,
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
                        'token'             => 67,
                        'name'              => '$var1',
                        'content'           => '$var1=1',
                        'default'           => '1',
                        'pass_by_reference' => false,
                        'variable_length'   => false,
                        'type_hint'         => '',
                        'nullable_type'     => false,
                       );
        $expected[1] = array(
                        'token'             => 72,
                        'name'              => '$var2',
                        'content'           => "\$var2='value'",
                        'default'           => "'value'",
                        'pass_by_reference' => false,
                        'variable_length'   => false,
                        'type_hint'         => '',
                        'nullable_type'     => false,
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

