<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:getMethodParameters method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Files\DummyFile;

class GetMethodParametersTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The PHP_CodeSniffer_File object containing parsed contents of this file.
     *
     * @var PHP_CodeSniffer_File
     */
    private $phpcsFile;


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
        $config            = new Config();
        $config->standards = array('Generic');
        $config->sniffs    = array('Generic.None.None');

        $ruleset = new Ruleset($config);

        $this->phpcsFile = new DummyFile(file_get_contents(__FILE__), $ruleset, $config);
        $this->phpcsFile->process();

    }//end setUp()


    /**
     * Clean up after finished test.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->phpcsFile);

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
                        'variable_length'   => false,
                        'type_hint'         => '',
                       );

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testPassByReference */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
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
                        'variable_length'   => false,
                        'type_hint'         => 'array',
                       );

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testArrayHint */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
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
                        'variable_length'   => false,
                        'type_hint'         => 'foo',
                       );

        $expected[1] = array(
                        'name'              => '$var2',
                        'pass_by_reference' => false,
                        'variable_length'   => false,
                        'type_hint'         => 'bar',
                       );

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testTypeHint */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
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
                        'variable_length'   => false,
                        'type_hint'         => '',
                       );

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testVariable */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
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
                        'variable_length'   => false,
                        'type_hint'         => '',
                       );

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testSingleDefaultValue */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
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
                        'variable_length'   => false,
                        'type_hint'         => '',
                       );
        $expected[1] = array(
                        'name'              => '$var2',
                        'default'           => "'value'",
                        'pass_by_reference' => false,
                        'variable_length'   => false,
                        'type_hint'         => '',
                       );

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testDefaultValues */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
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
