<?php
/**
 * Tests the conversion of square bracket tokens to short array tokens.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class ShortArrayTest extends AbstractMethodUnitTest
{


    /**
     * Test that real square brackets are still tokenized as square brackets.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataSquareBrackets
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testSquareBrackets($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $opener = $this->getTargetToken($testMarker, [T_OPEN_SQUARE_BRACKET, T_OPEN_SHORT_ARRAY]);
        $this->assertSame(T_OPEN_SQUARE_BRACKET, $tokens[$opener]['code']);
        $this->assertSame('T_OPEN_SQUARE_BRACKET', $tokens[$opener]['type']);

        if (isset($tokens[$opener]['bracket_closer']) === true) {
            $closer = $tokens[$opener]['bracket_closer'];
            $this->assertSame(T_CLOSE_SQUARE_BRACKET, $tokens[$closer]['code']);
            $this->assertSame('T_CLOSE_SQUARE_BRACKET', $tokens[$closer]['type']);
        }

    }//end testSquareBrackets()


    /**
     * Data provider.
     *
     * @see testSquareBrackets()
     *
     * @return array
     */
    public function dataSquareBrackets()
    {
        return [
            ['/* testArrayAccess1 */'],
            ['/* testArrayAccess2 */'],
            ['/* testArrayAssignment */'],
            ['/* testFunctionCallDereferencing */'],
            ['/* testMethodCallDereferencing */'],
            ['/* testStaticMethodCallDereferencing */'],
            ['/* testPropertyDereferencing */'],
            ['/* testPropertyDereferencingWithInaccessibleName */'],
            ['/* testStaticPropertyDereferencing */'],
            ['/* testStringDereferencing */'],
            ['/* testStringDereferencingDoubleQuoted */'],
            ['/* testConstantDereferencing */'],
            ['/* testClassConstantDereferencing */'],
            ['/* testMagicConstantDereferencing */'],
            ['/* testArrayAccessCurlyBraces */'],
            ['/* testArrayLiteralDereferencing */'],
            ['/* testShortArrayLiteralDereferencing */'],
            ['/* testClassMemberDereferencingOnInstantiation1 */'],
            ['/* testClassMemberDereferencingOnInstantiation2 */'],
            ['/* testClassMemberDereferencingOnClone */'],
            ['/* testLiveCoding */'],
        ];

    }//end dataSquareBrackets()


    /**
     * Test that short arrays and short lists are still tokenized as short arrays.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataShortArrays
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testShortArrays($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $opener = $this->getTargetToken($testMarker, [T_OPEN_SQUARE_BRACKET, T_OPEN_SHORT_ARRAY]);
        $this->assertSame(T_OPEN_SHORT_ARRAY, $tokens[$opener]['code']);
        $this->assertSame('T_OPEN_SHORT_ARRAY', $tokens[$opener]['type']);

        if (isset($tokens[$opener]['bracket_closer']) === true) {
            $closer = $tokens[$opener]['bracket_closer'];
            $this->assertSame(T_CLOSE_SHORT_ARRAY, $tokens[$closer]['code']);
            $this->assertSame('T_CLOSE_SHORT_ARRAY', $tokens[$closer]['type']);
        }

    }//end testShortArrays()


    /**
     * Data provider.
     *
     * @see testShortArrays()
     *
     * @return array
     */
    public function dataShortArrays()
    {
        return [
            ['/* testShortArrayDeclarationEmpty */'],
            ['/* testShortArrayDeclarationWithOneValue */'],
            ['/* testShortArrayDeclarationWithMultipleValues */'],
            ['/* testShortArrayDeclarationWithDereferencing */'],
            ['/* testShortListDeclaration */'],
            ['/* testNestedListDeclaration */'],
            ['/* testArrayWithinFunctionCall */'],
        ];

    }//end dataShortArrays()


}//end class
