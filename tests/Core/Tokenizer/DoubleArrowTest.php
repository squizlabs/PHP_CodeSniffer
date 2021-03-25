<?php
/**
 * Tests the retokenization of the double arrow to T_MATCH_ARROW for PHP 8.0 match structures
 * and makes sure that the tokenization of other double arrows (array, arrow function, yield)
 * is not aversely affected.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020-2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class DoubleArrowTest extends AbstractMethodUnitTest
{


    /**
     * Test that "normal" double arrows are correctly tokenized as `T_DOUBLE_ARROW`.
     *
     * @param string $testMarker The comment prefacing the target token.
     *
     * @dataProvider  dataDoubleArrow
     * @coversNothing
     *
     * @return void
     */
    public function testDoubleArrow($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_DOUBLE_ARROW, T_MATCH_ARROW, T_FN_ARROW]);
        $tokenArray = $tokens[$token];

        $this->assertSame(T_DOUBLE_ARROW, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_DOUBLE_ARROW (code)');
        $this->assertSame('T_DOUBLE_ARROW', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_DOUBLE_ARROW (type)');

    }//end testDoubleArrow()


    /**
     * Data provider.
     *
     * @see testDoubleArrow()
     *
     * @return array
     */
    public function dataDoubleArrow()
    {
        return [
            'simple_long_array'                         => ['/* testLongArrayArrowSimple */'],
            'simple_short_array'                        => ['/* testShortArrayArrowSimple */'],
            'simple_long_list'                          => ['/* testLongListArrowSimple */'],
            'simple_short_list'                         => ['/* testShortListArrowSimple */'],
            'simple_yield'                              => ['/* testYieldArrowSimple */'],
            'simple_foreach'                            => ['/* testForeachArrowSimple */'],

            'long_array_with_match_value_1'             => ['/* testLongArrayArrowWithNestedMatchValue1 */'],
            'long_array_with_match_value_2'             => ['/* testLongArrayArrowWithNestedMatchValue2 */'],
            'short_array_with_match_value_1'            => ['/* testShortArrayArrowWithNestedMatchValue1 */'],
            'short_array_with_match_value_2'            => ['/* testShortArrayArrowWithNestedMatchValue2 */'],

            'long_array_with_match_key'                 => ['/* testLongArrayArrowWithMatchKey */'],
            'short_array_with_match_key'                => ['/* testShortArrayArrowWithMatchKey */'],

            'long_array_in_match_body_1'                => ['/* testLongArrayArrowInMatchBody1 */'],
            'long_array_in_match_body_2'                => ['/* testLongArrayArrowInMatchBody2 */'],
            'long_array_in_match_body_2'                => ['/* testLongArrayArrowInMatchBody3 */'],
            'short_array_in_match_body_1'               => ['/* testShortArrayArrowInMatchBody1 */'],
            'short_array_in_match_body_2'               => ['/* testShortArrayArrowInMatchBody2 */'],
            'short_array_in_match_body_2'               => ['/* testShortArrayArrowInMatchBody3 */'],

            'short_array_in_match_case_1'               => ['/* testShortArrayArrowinMatchCase1 */'],
            'short_array_in_match_case_2'               => ['/* testShortArrayArrowinMatchCase2 */'],
            'short_array_in_match_case_3'               => ['/* testShortArrayArrowinMatchCase3 */'],
            'long_array_in_match_case_4'                => ['/* testLongArrayArrowinMatchCase4 */'],

            'in_complex_short_array_key_match_value'    => ['/* testShortArrayArrowInComplexMatchValueinShortArrayKey */'],
            'in_complex_short_array_toplevel'           => ['/* testShortArrayArrowInComplexMatchArrayMismash */'],
            'in_complex_short_array_value_match_value'  => ['/* testShortArrayArrowInComplexMatchValueinShortArrayValue */'],

            'long_list_in_match_body'                   => ['/* testLongListArrowInMatchBody */'],
            'long_list_in_match_case'                   => ['/* testLongListArrowInMatchCase */'],
            'short_list_in_match_body'                  => ['/* testShortListArrowInMatchBody */'],
            'short_list_in_match_case'                  => ['/* testShortListArrowInMatchCase */'],
            'long_list_with_match_in_key'               => ['/* testLongListArrowWithMatchInKey */'],
            'short_list_with_match_in_key'              => ['/* testShortListArrowWithMatchInKey */'],

            'long_array_with_constant_default_in_key'   => ['/* testLongArrayArrowWithClassConstantKey */'],
            'short_array_with_constant_default_in_key'  => ['/* testShortArrayArrowWithClassConstantKey */'],
            'yield_with_constant_default_in_key'        => ['/* testYieldArrowWithClassConstantKey */'],

            'long_array_with_default_in_key_in_match'   => ['/* testLongArrayArrowWithClassConstantKeyNestedInMatch */'],
            'short_array_with_default_in_key_in_match'  => ['/* testShortArrayArrowWithClassConstantKeyNestedInMatch */'],
            'long_array_with_default_in_key_with_match' => ['/* testLongArrayArrowWithClassConstantKeyWithNestedMatch */'],
            'long_array_with_default_in_key_with_match' => ['/* testShortArrayArrowWithClassConstantKeyWithNestedMatch */'],
        ];

    }//end dataDoubleArrow()


    /**
     * Test that double arrows in match expressions which are the demarkation between a case and the return value
     * are correctly tokenized as `T_MATCH_ARROW`.
     *
     * @param string $testMarker The comment prefacing the target token.
     *
     * @dataProvider dataMatchArrow
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testMatchArrow($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_DOUBLE_ARROW, T_MATCH_ARROW, T_FN_ARROW]);
        $tokenArray = $tokens[$token];

        $this->assertSame(T_MATCH_ARROW, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_MATCH_ARROW (code)');
        $this->assertSame('T_MATCH_ARROW', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_MATCH_ARROW (type)');

    }//end testMatchArrow()


    /**
     * Data provider.
     *
     * @see testMatchArrow()
     *
     * @return array
     */
    public function dataMatchArrow()
    {
        return [
            'single_case'                             => ['/* testMatchArrowSimpleSingleCase */'],
            'multi_case'                              => ['/* testMatchArrowSimpleMultiCase */'],
            'single_case_with_trailing_comma'         => ['/* testMatchArrowSimpleSingleCaseWithTrailingComma */'],
            'multi_case_with_trailing_comma'          => ['/* testMatchArrowSimpleMultiCaseWithTrailingComma */'],
            'match_nested_outer'                      => ['/* testMatchArrowNestedMatchOuter */'],
            'match_nested_inner'                      => ['/* testMatchArrowNestedMatchInner */'],

            'in_long_array_value_1'                   => ['/* testMatchArrowInLongArrayValue1 */'],
            'in_long_array_value_2'                   => ['/* testMatchArrowInLongArrayValue2 */'],
            'in_long_array_value_3'                   => ['/* testMatchArrowInLongArrayValue3 */'],
            'in_short_array_value_1'                  => ['/* testMatchArrowInShortArrayValue1 */'],
            'in_short_array_value_2'                  => ['/* testMatchArrowInShortArrayValue2 */'],
            'in_short_array_value_3'                  => ['/* testMatchArrowInShortArrayValue3 */'],

            'in_long_array_key_1'                     => ['/* testMatchArrowInLongArrayKey1 */'],
            'in_long_array_key_2'                     => ['/* testMatchArrowInLongArrayKey2 */'],
            'in_short_array_key_1'                    => ['/* testMatchArrowInShortArrayKey1 */'],
            'in_short_array_key_2'                    => ['/* testMatchArrowInShortArrayKey2 */'],

            'with_long_array_value_with_keys'         => ['/* testMatchArrowWithLongArrayBodyWithKeys */'],
            'with_short_array_value_without_keys'     => ['/* testMatchArrowWithShortArrayBodyWithoutKeys */'],
            'with_long_array_value_without_keys'      => ['/* testMatchArrowWithLongArrayBodyWithoutKeys */'],
            'with_short_array_value_with_keys'        => ['/* testMatchArrowWithShortArrayBodyWithKeys */'],

            'with_short_array_with_keys_as_case'      => ['/* testMatchArrowWithShortArrayWithKeysAsCase */'],
            'with_multiple_arrays_with_keys_as_case'  => ['/* testMatchArrowWithMultipleArraysWithKeysAsCase */'],

            'in_fn_body_case'                         => ['/* testMatchArrowInFnBody1 */'],
            'in_fn_body_default'                      => ['/* testMatchArrowInFnBody2 */'],
            'with_fn_body_case'                       => ['/* testMatchArrowWithFnBody1 */'],
            'with_fn_body_default'                    => ['/* testMatchArrowWithFnBody2 */'],

            'in_complex_short_array_key_1'            => ['/* testMatchArrowInComplexShortArrayKey1 */'],
            'in_complex_short_array_key_2'            => ['/* testMatchArrowInComplexShortArrayKey2 */'],
            'in_complex_short_array_value_1'          => ['/* testMatchArrowInComplexShortArrayValue1 */'],
            'in_complex_short_array_key_2'            => ['/* testMatchArrowInComplexShortArrayValue1 */'],

            'with_long_list_in_body'                  => ['/* testMatchArrowWithLongListBody */'],
            'with_long_list_in_case'                  => ['/* testMatchArrowWithLongListInCase */'],
            'with_short_list_in_body'                 => ['/* testMatchArrowWithShortListBody */'],
            'with_short_list_in_case'                 => ['/* testMatchArrowWithShortListInCase */'],
            'in_long_list_key'                        => ['/* testMatchArrowInLongListKey */'],
            'in_short_list_key'                       => ['/* testMatchArrowInShortListKey */'],

            'with_long_array_value_with_default_key'  => ['/* testMatchArrowWithNestedLongArrayWithClassConstantKey */'],
            'with_short_array_value_with_default_key' => ['/* testMatchArrowWithNestedShortArrayWithClassConstantKey */'],
            'in_long_array_value_with_default_key'    => ['/* testMatchArrowNestedInLongArrayWithClassConstantKey */'],
            'in_short_array_value_with_default_key'   => ['/* testMatchArrowNestedInShortArrayWithClassConstantKey */'],
        ];

    }//end dataMatchArrow()


    /**
     * Test that double arrows used as the scope opener for an arrow function
     * are correctly tokenized as `T_FN_ARROW`.
     *
     * @param string $testMarker The comment prefacing the target token.
     *
     * @dataProvider dataFnArrow
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testFnArrow($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_DOUBLE_ARROW, T_MATCH_ARROW, T_FN_ARROW]);
        $tokenArray = $tokens[$token];

        $this->assertSame(T_FN_ARROW, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_FN_ARROW (code)');
        $this->assertSame('T_FN_ARROW', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_FN_ARROW (type)');

    }//end testFnArrow()


    /**
     * Data provider.
     *
     * @see testFnArrow()
     *
     * @return array
     */
    public function dataFnArrow()
    {
        return [
            'simple_fn'                             => ['/* testFnArrowSimple */'],

            'with_match_as_value'                   => ['/* testFnArrowWithMatchInValue */'],
            'in_match_value_case'                   => ['/* testFnArrowInMatchBody1 */'],
            'in_match_value_default'                => ['/* testFnArrowInMatchBody2 */'],

            'in_complex_match_value_in_short_array' => ['/* testFnArrowInComplexMatchValueInShortArrayValue */'],
        ];

    }//end dataFnArrow()


}//end class
