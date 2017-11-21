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
use PHPUnit\Framework\TestCase;

class GetMethodParametersTest extends TestCase
{

    /**
     * The PHP_CodeSniffer_File object containing parsed contents of the test case file.
     *
     * @var \PHP_CodeSniffer\Files\File
     */
    private $phpcsFile;


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
        $config            = new Config();
        $config->standards = ['Generic'];

        $ruleset = new Ruleset($config);

        $pathToTestFile  = dirname(__FILE__).'/'.basename(__FILE__, '.php').'.inc';
        $this->phpcsFile = new DummyFile(file_get_contents($pathToTestFile), $ruleset, $config);
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
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => '&$var',
            'pass_by_reference' => true,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testPassByReference */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        $this->assertSame($expected, $found);

    }//end testPassByReference()


    /**
     * Verify array hint parsing.
     *
     * @return void
     */
    public function testArrayHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'array $var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'array',
            'nullable_type'     => false,
        ];

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testArrayHint */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        $this->assertSame($expected, $found);

    }//end testArrayHint()


    /**
     * Verify type hint parsing.
     *
     * @return void
     */
    public function testTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => 'foo $var1',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'foo',
            'nullable_type'     => false,
        ];

        $expected[1] = [
            'name'              => '$var2',
            'content'           => 'bar $var2',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'bar',
            'nullable_type'     => false,
        ];

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testTypeHint */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        unset($found[1]['token']);
        $this->assertSame($expected, $found);

    }//end testTypeHint()


    /**
     * Verify self type hint parsing.
     *
     * @return void
     */
    public function testSelfTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'self $var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'self',
            'nullable_type'     => false,
        ];

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testSelfTypeHint */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        $this->assertSame($expected, $found);

    }//end testSelfTypeHint()


    /**
     * Verify nullable type hint parsing.
     *
     * @return void
     */
    public function testNullableTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => '?int $var1',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?int',
            'nullable_type'     => true,
        ];

        $expected[1] = [
            'name'              => '$var2',
            'content'           => '?\bar $var2',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?\bar',
            'nullable_type'     => true,
        ];

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testNullableTypeHint */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        unset($found[1]['token']);
        $this->assertSame($expected, $found);

    }//end testNullableTypeHint()


    /**
     * Verify variable.
     *
     * @return void
     */
    public function testVariable()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => '$var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testVariable */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        $this->assertSame($expected, $found);

    }//end testVariable()


    /**
     * Verify default value parsing with a single function param.
     *
     * @return void
     */
    public function testSingleDefaultValue()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => '$var1=self::CONSTANT',
            'default'           => 'self::CONSTANT',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testSingleDefaultValue */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        $this->assertSame($expected, $found);

    }//end testSingleDefaultValue()


    /**
     * Verify default value parsing.
     *
     * @return void
     */
    public function testDefaultValues()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => '$var1=1',
            'default'           => '1',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];
        $expected[1] = [
            'name'              => '$var2',
            'content'           => "\$var2='value'",
            'default'           => "'value'",
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testDefaultValues */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        unset($found[1]['token']);
        $this->assertSame($expected, $found);

    }//end testDefaultValues()


    /**
     * Verify "bitwise and" in default value !== pass-by-reference.
     *
     * @return void
     */
    public function testBitwiseAndConstantExpressionDefaultValue()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$a',
            'content'           => '$a = 10 & 20',
            'default'           => '10 & 20',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $start    = ($this->phpcsFile->numTokens - 1);
        $function = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testBitwiseAndConstantExpressionDefaultValue */'
        );

        $found = $this->phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        $this->assertSame($expected, $found);

    }//end testBitwiseAndConstantExpressionDefaultValue()


}//end class
