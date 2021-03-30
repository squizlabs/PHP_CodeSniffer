<?php
/**
 * Tests the backfilling of the T_FN token to PHP < 7.4.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class BackfillFnTokenTest extends AbstractMethodUnitTest
{


    /**
     * Test simple arrow functions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testSimple()
    {
        foreach (['/* testStandard */', '/* testMixedCase */'] as $comment) {
            $token = $this->getTargetToken($comment, T_FN);
            $this->backfillHelper($token);
            $this->scopePositionTestHelper($token, 5, 12);
        }

    }//end testSimple()


    /**
     * Test whitespace inside arrow function definitions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testWhitespace()
    {
        $token = $this->getTargetToken('/* testWhitespace */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 6, 13);

    }//end testWhitespace()


    /**
     * Test comments inside arrow function definitions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testComment()
    {
        $token = $this->getTargetToken('/* testComment */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 8, 15);

    }//end testComment()


    /**
     * Test heredocs inside arrow function definitions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testHeredoc()
    {
        $token = $this->getTargetToken('/* testHeredoc */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 4, 9);

    }//end testHeredoc()


    /**
     * Test nested arrow functions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNestedOuter()
    {
        $token = $this->getTargetToken('/* testNestedOuter */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 25);

    }//end testNestedOuter()


    /**
     * Test nested arrow functions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNestedInner()
    {
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testNestedInner */', T_FN);
        $this->backfillHelper($token, true);

        $expectedScopeOpener = ($token + 5);
        $expectedScopeCloser = ($token + 16);

        $this->assertSame($expectedScopeOpener, $tokens[$token]['scope_opener'], 'Scope opener is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$token]['scope_closer'], 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($expectedScopeOpener, $tokens[$opener]['scope_opener'], 'Opener scope opener is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$opener]['scope_closer'], 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame(($token - 4), $tokens[$closer]['scope_opener'], 'Closer scope opener is not the arrow token of the "outer" arrow function (shared scope closer)');
        $this->assertSame($expectedScopeCloser, $tokens[$closer]['scope_closer'], 'Closer scope closer is not the semicolon token');

    }//end testNestedInner()


    /**
     * Test arrow functions that call functions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testFunctionCall()
    {
        $token = $this->getTargetToken('/* testFunctionCall */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 17);

    }//end testFunctionCall()


    /**
     * Test arrow functions that are included in chained calls.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testChainedFunctionCall()
    {
        $token = $this->getTargetToken('/* testChainedFunctionCall */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 12, 'bracket');

    }//end testChainedFunctionCall()


    /**
     * Test arrow functions that are used as function arguments.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testFunctionArgument()
    {
        $token = $this->getTargetToken('/* testFunctionArgument */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 8, 15, 'comma');

    }//end testFunctionArgument()


    /**
     * Test arrow functions that use closures.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testClosure()
    {
        $token = $this->getTargetToken('/* testClosure */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 60, 'comma');

    }//end testClosure()


    /**
     * Test arrow functions using an array index.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testArrayIndex()
    {
        $token = $this->getTargetToken('/* testArrayIndex */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 8, 17, 'comma');

    }//end testArrayIndex()


    /**
     * Test arrow functions with a return type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testReturnType()
    {
        $token = $this->getTargetToken('/* testReturnType */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 11, 18, 'comma');

    }//end testReturnType()


    /**
     * Test arrow functions that return a reference.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testReference()
    {
        $token = $this->getTargetToken('/* testReference */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 6, 9);

    }//end testReference()


    /**
     * Test arrow functions that are grouped by parenthesis.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testGrouped()
    {
        $token = $this->getTargetToken('/* testGrouped */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 8);

    }//end testGrouped()


    /**
     * Test arrow functions that are used as array values.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testArrayValue()
    {
        $token = $this->getTargetToken('/* testArrayValue */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 4, 9, 'comma');

    }//end testArrayValue()


    /**
     * Test arrow functions that use the yield keyword.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testYield()
    {
        $token = $this->getTargetToken('/* testYield */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 14);

    }//end testYield()


    /**
     * Test arrow functions that use nullable namespace types.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNullableNamespace()
    {
        $token = $this->getTargetToken('/* testNullableNamespace */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 15, 18);

    }//end testNullableNamespace()


    /**
     * Test arrow functions that use the namespace operator in the return type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNamespaceOperatorInTypes()
    {
        $token = $this->getTargetToken('/* testNamespaceOperatorInTypes */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 16, 19);

    }//end testNamespaceOperatorInTypes()


    /**
     * Test arrow functions that use self/parent/callable/array/static return types.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testKeywordReturnTypes()
    {
        $tokens = self::$phpcsFile->getTokens();

        $testMarkers = [
            'Self',
            'Parent',
            'Callable',
            'Array',
            'Static',
        ];

        foreach ($testMarkers as $marker) {
            $token = $this->getTargetToken('/* test'.$marker.'ReturnType */', T_FN);
            $this->backfillHelper($token);

            $expectedScopeOpener = ($token + 11);
            $expectedScopeCloser = ($token + 14);

            $this->assertSame($expectedScopeOpener, $tokens[$token]['scope_opener'], "Scope opener is not the arrow token (for $marker)");
            $this->assertSame($expectedScopeCloser, $tokens[$token]['scope_closer'], "Scope closer is not the semicolon token(for $marker)");

            $opener = $tokens[$token]['scope_opener'];
            $this->assertSame($expectedScopeOpener, $tokens[$opener]['scope_opener'], "Opener scope opener is not the arrow token(for $marker)");
            $this->assertSame($expectedScopeCloser, $tokens[$opener]['scope_closer'], "Opener scope closer is not the semicolon token(for $marker)");

            $closer = $tokens[$token]['scope_closer'];
            $this->assertSame($expectedScopeOpener, $tokens[$closer]['scope_opener'], "Closer scope opener is not the arrow token(for $marker)");
            $this->assertSame($expectedScopeCloser, $tokens[$closer]['scope_closer'], "Closer scope closer is not the semicolon token(for $marker)");
        }

    }//end testKeywordReturnTypes()


    /**
     * Test arrow function with a union parameter type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testUnionParamType()
    {
        $token = $this->getTargetToken('/* testUnionParamType */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 13, 21);

    }//end testUnionParamType()


    /**
     * Test arrow function with a union return type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testUnionReturnType()
    {
        $token = $this->getTargetToken('/* testUnionReturnType */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 11, 18);

    }//end testUnionReturnType()


    /**
     * Test arrow functions used in ternary operators.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testTernary()
    {
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testTernary */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 40);

        $token = $this->getTargetToken('/* testTernaryThen */', T_FN);
        $this->backfillHelper($token);

        $expectedScopeOpener = ($token + 8);
        $expectedScopeCloser = ($token + 12);

        $this->assertSame($expectedScopeOpener, $tokens[$token]['scope_opener'], 'Scope opener for THEN is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$token]['scope_closer'], 'Scope closer for THEN is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($expectedScopeOpener, $tokens[$opener]['scope_opener'], 'Opener scope opener for THEN is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$opener]['scope_closer'], 'Opener scope closer for THEN is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($expectedScopeOpener, $tokens[$closer]['scope_opener'], 'Closer scope opener for THEN is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$closer]['scope_closer'], 'Closer scope closer for THEN is not the semicolon token');

        $token = $this->getTargetToken('/* testTernaryElse */', T_FN);
        $this->backfillHelper($token, true);

        $expectedScopeOpener = ($token + 8);
        $expectedScopeCloser = ($token + 11);

        $this->assertSame($expectedScopeOpener, $tokens[$token]['scope_opener'], 'Scope opener for ELSE is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$token]['scope_closer'], 'Scope closer for ELSE is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($expectedScopeOpener, $tokens[$opener]['scope_opener'], 'Opener scope opener for ELSE is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$opener]['scope_closer'], 'Opener scope closer for ELSE is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame(($token - 24), $tokens[$closer]['scope_opener'], 'Closer scope opener for ELSE is not the arrow token of the "outer" arrow function (shared scope closer)');
        $this->assertSame($expectedScopeCloser, $tokens[$closer]['scope_closer'], 'Closer scope closer for ELSE is not the semicolon token');

    }//end testTernary()


    /**
     * Test arrow function returning a match control structure.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testWithMatchValue()
    {
        $token = $this->getTargetToken('/* testWithMatchValue */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 44);

    }//end testWithMatchValue()


    /**
     * Test arrow function returning a match control structure with something behind it.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testWithMatchValueAndMore()
    {
        $token = $this->getTargetToken('/* testWithMatchValueAndMore */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 48);

    }//end testWithMatchValueAndMore()


    /**
     * Test match control structure returning arrow functions.
     *
     * @param string $testMarker                 The comment prefacing the target token.
     * @param int    $openerOffset               The expected offset of the scope opener in relation to the testMarker.
     * @param int    $closerOffset               The expected offset of the scope closer in relation to the testMarker.
     * @param string $expectedCloserType         The type of token expected for the scope closer.
     * @param string $expectedCloserFriendlyName A friendly name for the type of token expected for the scope closer
     *                                           to be used in the error message for failing tests.
     *
     * @dataProvider dataInMatchValue
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testInMatchValue($testMarker, $openerOffset, $closerOffset, $expectedCloserType, $expectedCloserFriendlyName)
    {
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken($testMarker, T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, $openerOffset, $closerOffset, $expectedCloserFriendlyName);

        $this->assertSame($expectedCloserType, $tokens[($token + $closerOffset)]['type'], 'Mismatched scope closer type');

    }//end testInMatchValue()


    /**
     * Data provider.
     *
     * @see testInMatchValue()
     *
     * @return array
     */
    public function dataInMatchValue()
    {
        return [
            'not_last_value'                      => [
                '/* testInMatchNotLastValue */',
                5,
                11,
                'T_COMMA',
                'comma',
            ],
            'last_value_with_trailing_comma'      => [
                '/* testInMatchLastValueWithTrailingComma */',
                5,
                11,
                'T_COMMA',
                'comma',
            ],
            'last_value_without_trailing_comma_1' => [
                '/* testInMatchLastValueNoTrailingComma1 */',
                5,
                10,
                'T_CLOSE_PARENTHESIS',
                'close parenthesis',
            ],
            'last_value_without_trailing_comma_2' => [
                '/* testInMatchLastValueNoTrailingComma2 */',
                5,
                11,
                'T_VARIABLE',
                '$y variable',
            ],
        ];

    }//end dataInMatchValue()


    /**
     * Test arrow function nested within a method declaration.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNestedInMethod()
    {
        $token = $this->getTargetToken('/* testNestedInMethod */', T_FN);
        $this->backfillHelper($token);
        $this->scopePositionTestHelper($token, 5, 17);

    }//end testNestedInMethod()


    /**
     * Verify that "fn" keywords which are not arrow functions get tokenized as T_STRING and don't
     * have the extra token array indexes.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to look for.
     *
     * @dataProvider dataNotAnArrowFunction
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNotAnArrowFunction($testMarker, $testContent='fn')
    {
        $tokens = self::$phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_STRING, T_FN], $testContent);
        $tokenArray = $tokens[$token];

        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING');

        $this->assertArrayNotHasKey('scope_condition', $tokenArray, 'Scope condition is set');
        $this->assertArrayNotHasKey('scope_opener', $tokenArray, 'Scope opener is set');
        $this->assertArrayNotHasKey('scope_closer', $tokenArray, 'Scope closer is set');
        $this->assertArrayNotHasKey('parenthesis_owner', $tokenArray, 'Parenthesis owner is set');
        $this->assertArrayNotHasKey('parenthesis_opener', $tokenArray, 'Parenthesis opener is set');
        $this->assertArrayNotHasKey('parenthesis_closer', $tokenArray, 'Parenthesis closer is set');

    }//end testNotAnArrowFunction()


    /**
     * Data provider.
     *
     * @see testNotAnArrowFunction()
     *
     * @return array
     */
    public function dataNotAnArrowFunction()
    {
        return [
            ['/* testFunctionName */'],
            [
                '/* testConstantDeclaration */',
                'FN',
            ],
            [
                '/* testConstantDeclarationLower */',
                'fn',
            ],
            ['/* testStaticMethodName */'],
            ['/* testPropertyAssignment */'],
            [
                '/* testAnonClassMethodName */',
                'fN',
            ],
            ['/* testNonArrowStaticMethodCall */'],
            [
                '/* testNonArrowConstantAccess */',
                'FN',
            ],
            [
                '/* testNonArrowConstantAccessMixed */',
                'Fn',
            ],
            ['/* testNonArrowObjectMethodCall */'],
            [
                '/* testNonArrowObjectMethodCallUpper */',
                'FN',
            ],
            [
                '/* testNonArrowNamespacedFunctionCall */',
                'Fn',
            ],
            ['/* testNonArrowNamespaceOperatorFunctionCall */'],
            ['/* testNonArrowFunctionNameWithUnionTypes */'],
            ['/* testLiveCoding */'],
        ];

    }//end dataNotAnArrowFunction()


    /**
     * Helper function to check that all token keys are correctly set for T_FN tokens.
     *
     * @param int  $token                The T_FN token to check.
     * @param bool $skipScopeCloserCheck Whether to skip the scope closer check.
     *                                   This should be set to "true" when testing nested arrow functions,
     *                                   where the "inner" arrow function shares a scope closer with the
     *                                   "outer" arrow function, as the 'scope_condition' for the scope closer
     *                                   of the "inner" arrow function will point to the "outer" arrow function.
     *
     * @return void
     */
    private function backfillHelper($token, $skipScopeCloserCheck=false)
    {
        $tokens = self::$phpcsFile->getTokens();

        $this->assertTrue(array_key_exists('scope_condition', $tokens[$token]), 'Scope condition is not set');
        $this->assertTrue(array_key_exists('scope_opener', $tokens[$token]), 'Scope opener is not set');
        $this->assertTrue(array_key_exists('scope_closer', $tokens[$token]), 'Scope closer is not set');
        $this->assertSame($tokens[$token]['scope_condition'], $token, 'Scope condition is not the T_FN token');
        $this->assertTrue(array_key_exists('parenthesis_owner', $tokens[$token]), 'Parenthesis owner is not set');
        $this->assertTrue(array_key_exists('parenthesis_opener', $tokens[$token]), 'Parenthesis opener is not set');
        $this->assertTrue(array_key_exists('parenthesis_closer', $tokens[$token]), 'Parenthesis closer is not set');
        $this->assertSame($tokens[$token]['parenthesis_owner'], $token, 'Parenthesis owner is not the T_FN token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertTrue(array_key_exists('scope_condition', $tokens[$opener]), 'Opener scope condition is not set');
        $this->assertTrue(array_key_exists('scope_opener', $tokens[$opener]), 'Opener scope opener is not set');
        $this->assertTrue(array_key_exists('scope_closer', $tokens[$opener]), 'Opener scope closer is not set');
        $this->assertSame($tokens[$opener]['scope_condition'], $token, 'Opener scope condition is not the T_FN token');
        $this->assertSame(T_FN_ARROW, $tokens[$opener]['code'], 'Arrow scope opener not tokenized as T_FN_ARROW (code)');
        $this->assertSame('T_FN_ARROW', $tokens[$opener]['type'], 'Arrow scope opener not tokenized as T_FN_ARROW (type)');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertTrue(array_key_exists('scope_condition', $tokens[$closer]), 'Closer scope condition is not set');
        $this->assertTrue(array_key_exists('scope_opener', $tokens[$closer]), 'Closer scope opener is not set');
        $this->assertTrue(array_key_exists('scope_closer', $tokens[$closer]), 'Closer scope closer is not set');
        if ($skipScopeCloserCheck === false) {
            $this->assertSame($tokens[$closer]['scope_condition'], $token, 'Closer scope condition is not the T_FN token');
        }

        $opener = $tokens[$token]['parenthesis_opener'];
        $this->assertTrue(array_key_exists('parenthesis_owner', $tokens[$opener]), 'Opening parenthesis owner is not set');
        $this->assertSame($tokens[$opener]['parenthesis_owner'], $token, 'Opening parenthesis owner is not the T_FN token');

        $closer = $tokens[$token]['parenthesis_closer'];
        $this->assertTrue(array_key_exists('parenthesis_owner', $tokens[$closer]), 'Closing parenthesis owner is not set');
        $this->assertSame($tokens[$closer]['parenthesis_owner'], $token, 'Closing parenthesis owner is not the T_FN token');

    }//end backfillHelper()


    /**
     * Helper function to check that the scope opener/closer positions are correctly set for T_FN tokens.
     *
     * @param int    $token              The T_FN token to check.
     * @param int    $openerOffset       The expected offset of the scope opener in relation to
     *                                   the fn keyword.
     * @param int    $closerOffset       The expected offset of the scope closer in relation to
     *                                   the fn keyword.
     * @param string $expectedCloserType Optional. The type of token expected for the scope closer.
     *
     * @return void
     */
    private function scopePositionTestHelper($token, $openerOffset, $closerOffset, $expectedCloserType='semicolon')
    {
        $tokens = self::$phpcsFile->getTokens();
        $expectedScopeOpener = ($token + $openerOffset);
        $expectedScopeCloser = ($token + $closerOffset);

        $this->assertSame($expectedScopeOpener, $tokens[$token]['scope_opener'], 'Scope opener is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$token]['scope_closer'], 'Scope closer is not the '.$expectedCloserType.' token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($expectedScopeOpener, $tokens[$opener]['scope_opener'], 'Opener scope opener is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$opener]['scope_closer'], 'Opener scope closer is not the '.$expectedCloserType.' token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($expectedScopeOpener, $tokens[$closer]['scope_opener'], 'Closer scope opener is not the arrow token');
        $this->assertSame($expectedScopeCloser, $tokens[$closer]['scope_closer'], 'Closer scope closer is not the '.$expectedCloserType.' token');

    }//end scopePositionTestHelper()


}//end class
