<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:getMethodParameters method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class GetMethodParametersTest extends AbstractMethodUnitTest
{


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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testPassByReference */'
        );

        $found = self::$phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        unset($found[0]['type_hint_token']);
        unset($found[0]['type_hint_end_token']);
        unset($found[0]['comma_token']);
        unset($found[0]['reference_token']);
        unset($found[0]['variadic_token']);
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testArrayHint */'
        );

        $found = self::$phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        unset($found[0]['type_hint_token']);
        unset($found[0]['type_hint_end_token']);
        unset($found[0]['comma_token']);
        unset($found[0]['reference_token']);
        unset($found[0]['variadic_token']);
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testTypeHint */'
        );

        $found = self::$phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        unset($found[1]['token']);
        unset($found[0]['type_hint_token']);
        unset($found[1]['type_hint_token']);
        unset($found[0]['type_hint_end_token']);
        unset($found[1]['type_hint_end_token']);
        unset($found[0]['comma_token']);
        unset($found[1]['comma_token']);
        unset($found[0]['reference_token']);
        unset($found[1]['reference_token']);
        unset($found[0]['variadic_token']);
        unset($found[1]['variadic_token']);
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testSelfTypeHint */'
        );

        $found = self::$phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        unset($found[0]['type_hint_token']);
        unset($found[0]['type_hint_end_token']);
        unset($found[0]['comma_token']);
        unset($found[0]['reference_token']);
        unset($found[0]['variadic_token']);
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testNullableTypeHint */'
        );

        $found = self::$phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        unset($found[1]['token']);
        unset($found[0]['type_hint_token']);
        unset($found[1]['type_hint_token']);
        unset($found[0]['type_hint_end_token']);
        unset($found[1]['type_hint_end_token']);
        unset($found[0]['comma_token']);
        unset($found[1]['comma_token']);
        unset($found[0]['reference_token']);
        unset($found[1]['reference_token']);
        unset($found[0]['variadic_token']);
        unset($found[1]['variadic_token']);
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testVariable */'
        );

        $found = self::$phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        unset($found[0]['type_hint_token']);
        unset($found[0]['type_hint_end_token']);
        unset($found[0]['comma_token']);
        unset($found[0]['reference_token']);
        unset($found[0]['variadic_token']);
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testSingleDefaultValue */'
        );

        $found = self::$phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        unset($found[0]['type_hint_token']);
        unset($found[0]['type_hint_end_token']);
        unset($found[0]['comma_token']);
        unset($found[0]['reference_token']);
        unset($found[0]['variadic_token']);
        unset($found[0]['default_token']);
        unset($found[0]['default_equal_token']);
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testDefaultValues */'
        );

        $found = self::$phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        unset($found[1]['token']);
        unset($found[0]['type_hint_token']);
        unset($found[1]['type_hint_token']);
        unset($found[0]['type_hint_end_token']);
        unset($found[1]['type_hint_end_token']);
        unset($found[0]['comma_token']);
        unset($found[1]['comma_token']);
        unset($found[0]['reference_token']);
        unset($found[1]['reference_token']);
        unset($found[0]['variadic_token']);
        unset($found[1]['variadic_token']);
        unset($found[0]['default_token']);
        unset($found[1]['default_token']);
        unset($found[0]['default_equal_token']);
        unset($found[1]['default_equal_token']);
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testBitwiseAndConstantExpressionDefaultValue */'
        );

        $found = self::$phpcsFile->getMethodParameters(($function + 2));
        unset($found[0]['token']);
        unset($found[0]['type_hint_token']);
        unset($found[0]['type_hint_end_token']);
        unset($found[0]['comma_token']);
        unset($found[0]['reference_token']);
        unset($found[0]['variadic_token']);
        unset($found[0]['default_token']);
        unset($found[0]['default_equal_token']);
        $this->assertSame($expected, $found);

    }//end testBitwiseAndConstantExpressionDefaultValue()


}//end class
