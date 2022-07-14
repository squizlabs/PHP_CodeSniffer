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
            'array access 1'                                => ['/* testArrayAccess1 */'],
            'array access 2'                                => ['/* testArrayAccess2 */'],
            'array assignment'                              => ['/* testArrayAssignment */'],
            'function call dereferencing'                   => ['/* testFunctionCallDereferencing */'],
            'method call dereferencing'                     => ['/* testMethodCallDereferencing */'],
            'static method call dereferencing'              => ['/* testStaticMethodCallDereferencing */'],
            'property dereferencing'                        => ['/* testPropertyDereferencing */'],
            'property dereferencing with inaccessable name' => ['/* testPropertyDereferencingWithInaccessibleName */'],
            'static property dereferencing'                 => ['/* testStaticPropertyDereferencing */'],
            'string dereferencing single quotes'            => ['/* testStringDereferencing */'],
            'string dereferencing double quotes'            => ['/* testStringDereferencingDoubleQuoted */'],
            'global constant dereferencing'                 => ['/* testConstantDereferencing */'],
            'class constant dereferencing'                  => ['/* testClassConstantDereferencing */'],
            'magic constant dereferencing'                  => ['/* testMagicConstantDereferencing */'],
            'array access with curly braces'                => ['/* testArrayAccessCurlyBraces */'],
            'array literal dereferencing'                   => ['/* testArrayLiteralDereferencing */'],
            'short array literal dereferencing'             => ['/* testShortArrayLiteralDereferencing */'],
            'class member dereferencing on instantiation 1' => ['/* testClassMemberDereferencingOnInstantiation1 */'],
            'class member dereferencing on instantiation 2' => ['/* testClassMemberDereferencingOnInstantiation2 */'],
            'class member dereferencing on clone'           => ['/* testClassMemberDereferencingOnClone */'],
            'nullsafe method call dereferencing'            => ['/* testNullsafeMethodCallDereferencing */'],
            'interpolated string dereferencing'             => ['/* testInterpolatedStringDereferencing */'],
            'live coding'                                   => ['/* testLiveCoding */'],
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
            'short array empty'                              => ['/* testShortArrayDeclarationEmpty */'],
            'short array with value'                         => ['/* testShortArrayDeclarationWithOneValue */'],
            'short array with values'                        => ['/* testShortArrayDeclarationWithMultipleValues */'],
            'short array with dereferencing'                 => ['/* testShortArrayDeclarationWithDereferencing */'],
            'short list'                                     => ['/* testShortListDeclaration */'],
            'short list nested'                              => ['/* testNestedListDeclaration */'],
            'short array within function call'               => ['/* testArrayWithinFunctionCall */'],
            'short list after braced control structure'      => ['/* testShortListDeclarationAfterBracedControlStructure */'],
            'short list after non-braced control structure'  => ['/* testShortListDeclarationAfterNonBracedControlStructure */'],
            'short list after alternative control structure' => ['/* testShortListDeclarationAfterAlternativeControlStructure */'],
        ];

    }//end dataShortArrays()


}//end class
