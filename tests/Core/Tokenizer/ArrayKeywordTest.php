<?php
/**
 * Tests that the array keyword is tokenized correctly.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class ArrayKeywordTest extends AbstractMethodUnitTest
{


    /**
     * Test that the array keyword is correctly tokenized as `T_ARRAY`.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent Optional. The token content to look for.
     *
     * @dataProvider dataArrayKeyword
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createTokenMap
     *
     * @return void
     */
    public function testArrayKeyword($testMarker, $testContent='array')
    {
        $tokens = self::$phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_ARRAY, T_STRING], $testContent);
        $tokenArray = $tokens[$token];

        $this->assertSame(T_ARRAY, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_ARRAY (code)');
        $this->assertSame('T_ARRAY', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_ARRAY (type)');

        $this->assertArrayHasKey('parenthesis_owner', $tokenArray, 'Parenthesis owner is not set');
        $this->assertArrayHasKey('parenthesis_opener', $tokenArray, 'Parenthesis opener is not set');
        $this->assertArrayHasKey('parenthesis_closer', $tokenArray, 'Parenthesis closer is not set');

    }//end testArrayKeyword()


    /**
     * Data provider.
     *
     * @see testArrayKeyword()
     *
     * @return array
     */
    public function dataArrayKeyword()
    {
        return [
            'empty array'                           => ['/* testEmptyArray */'],
            'array with space before parenthesis'   => ['/* testArrayWithSpace */'],
            'array with comment before parenthesis' => [
                '/* testArrayWithComment */',
                'Array',
            ],
            'nested: outer array'                   => ['/* testNestingArray */'],
            'nested: inner array'                   => ['/* testNestedArray */'],
        ];

    }//end dataArrayKeyword()


    /**
     * Test that the array keyword when used in a type declaration is correctly tokenized as `T_STRING`.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent Optional. The token content to look for.
     *
     * @dataProvider dataArrayType
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createTokenMap
     *
     * @return void
     */
    public function testArrayType($testMarker, $testContent='array')
    {
        $tokens = self::$phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_ARRAY, T_STRING], $testContent);
        $tokenArray = $tokens[$token];

        $this->assertSame(T_STRING, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (code)');
        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (type)');

        $this->assertArrayNotHasKey('parenthesis_owner', $tokenArray, 'Parenthesis owner is set');
        $this->assertArrayNotHasKey('parenthesis_opener', $tokenArray, 'Parenthesis opener is set');
        $this->assertArrayNotHasKey('parenthesis_closer', $tokenArray, 'Parenthesis closer is set');

    }//end testArrayType()


    /**
     * Data provider.
     *
     * @see testArrayType()
     *
     * @return array
     */
    public function dataArrayType()
    {
        return [
            'closure return type'        => [
                '/* testClosureReturnType */',
                'Array',
            ],
            'function param type'        => ['/* testFunctionDeclarationParamType */'],
            'function union return type' => ['/* testFunctionDeclarationReturnType */'],
        ];

    }//end dataArrayType()


    /**
     * Verify that the retokenization of `T_ARRAY` tokens to `T_STRING` is handled correctly
     * for tokens with the contents 'array' which aren't in actual fact the array keyword.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to look for.
     *
     * @dataProvider dataNotArrayKeyword
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createTokenMap
     *
     * @return void
     */
    public function testNotArrayKeyword($testMarker, $testContent='array')
    {
        $tokens = self::$phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_ARRAY, T_STRING], $testContent);
        $tokenArray = $tokens[$token];

        $this->assertSame(T_STRING, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (code)');
        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (type)');

        $this->assertArrayNotHasKey('parenthesis_owner', $tokenArray, 'Parenthesis owner is set');
        $this->assertArrayNotHasKey('parenthesis_opener', $tokenArray, 'Parenthesis opener is set');
        $this->assertArrayNotHasKey('parenthesis_closer', $tokenArray, 'Parenthesis closer is set');

    }//end testNotArrayKeyword()


    /**
     * Data provider.
     *
     * @see testNotArrayKeyword()
     *
     * @return array
     */
    public function dataNotArrayKeyword()
    {
        return [
            'class-constant-name' => [
                '/* testClassConst */',
                'ARRAY',
            ],
            'class-method-name'   => ['/* testClassMethod */'],
        ];

    }//end dataNotArrayKeyword()


}//end class
