<?php
/**
 * Tests the conversion of bitwise and tokens to type intersection tokens.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @author    Jaroslav Hanslík <kukulich@kukulich.cz>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class TypeIntersectionTest extends AbstractMethodUnitTest
{


    /**
     * Test that non-intersection type bitwise and tokens are still tokenized as bitwise and.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataBitwiseAnd
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testBitwiseAnd($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $opener = $this->getTargetToken($testMarker, [T_BITWISE_AND, T_TYPE_INTERSECTION]);
        $this->assertSame(T_BITWISE_AND, $tokens[$opener]['code']);
        $this->assertSame('T_BITWISE_AND', $tokens[$opener]['type']);

    }//end testBitwiseAnd()


    /**
     * Data provider.
     *
     * @see testBitwiseAnd()
     *
     * @return array
     */
    public function dataBitwiseAnd()
    {
        return [
            ['/* testBitwiseAnd1 */'],
            ['/* testBitwiseAnd2 */'],
            ['/* testBitwiseAndPropertyDefaultValue */'],
            ['/* testBitwiseAndParamDefaultValue */'],
            ['/* testBitwiseAnd3 */'],
            ['/* testBitwiseAnd4 */'],
            ['/* testBitwiseAnd5 */'],
            ['/* testBitwiseAndClosureParamDefault */'],
            ['/* testBitwiseAndArrowParamDefault */'],
            ['/* testBitwiseAndArrowExpression */'],
            ['/* testBitwiseAndInArrayKey */'],
            ['/* testBitwiseAndInArrayValue */'],
            ['/* testBitwiseAndInShortArrayKey */'],
            ['/* testBitwiseAndInShortArrayValue */'],
            ['/* testBitwiseAndNonArrowFnFunctionCall */'],
            ['/* testBitwiseAnd6 */'],
            ['/* testLiveCoding */'],
        ];

    }//end dataBitwiseAnd()


    /**
     * Test that bitwise and tokens when used as part of a intersection type are tokenized as `T_TYPE_INTERSECTION`.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataTypeIntersection
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testTypeIntersection($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $opener = $this->getTargetToken($testMarker, [T_BITWISE_AND, T_TYPE_INTERSECTION]);
        $this->assertSame(T_TYPE_INTERSECTION, $tokens[$opener]['code']);
        $this->assertSame('T_TYPE_INTERSECTION', $tokens[$opener]['type']);

    }//end testTypeIntersection()


    /**
     * Data provider.
     *
     * @see testTypeIntersection()
     *
     * @return array
     */
    public function dataTypeIntersection()
    {
        return [
            ['/* testTypeIntersectionPropertySimple */'],
            ['/* testTypeIntersectionPropertyReverseModifierOrder */'],
            ['/* testTypeIntersectionPropertyMulti1 */'],
            ['/* testTypeIntersectionPropertyMulti2 */'],
            ['/* testTypeIntersectionPropertyMulti3 */'],
            ['/* testTypeIntersectionPropertyNamespaceRelative */'],
            ['/* testTypeIntersectionPropertyPartiallyQualified */'],
            ['/* testTypeIntersectionPropertyFullyQualified */'],
            ['/* testTypeIntersectionPropertyWithReadOnlyKeyword */'],
            ['/* testTypeIntersectionParam1 */'],
            ['/* testTypeIntersectionParam2 */'],
            ['/* testTypeIntersectionParam3 */'],
            ['/* testTypeIntersectionParamNamespaceRelative */'],
            ['/* testTypeIntersectionParamPartiallyQualified */'],
            ['/* testTypeIntersectionParamFullyQualified */'],
            ['/* testTypeIntersectionReturnType */'],
            ['/* testTypeIntersectionConstructorPropertyPromotion */'],
            ['/* testTypeIntersectionAbstractMethodReturnType1 */'],
            ['/* testTypeIntersectionAbstractMethodReturnType2 */'],
            ['/* testTypeIntersectionReturnTypeNamespaceRelative */'],
            ['/* testTypeIntersectionReturnPartiallyQualified */'],
            ['/* testTypeIntersectionReturnFullyQualified */'],
            ['/* testTypeIntersectionClosureParamIllegalNullable */'],
            ['/* testTypeIntersectionWithReference */'],
            ['/* testTypeIntersectionWithSpreadOperator */'],
            ['/* testTypeIntersectionClosureReturn */'],
            ['/* testTypeIntersectionArrowParam */'],
            ['/* testTypeIntersectionArrowReturnType */'],
            ['/* testTypeIntersectionNonArrowFunctionDeclaration */'],
            ['/* testTypeIntersectionWithInvalidTypes */'],
        ];

    }//end dataTypeIntersection()


}//end class
