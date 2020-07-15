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
        $tokens = self::$phpcsFile->getTokens();

        foreach (['/* testStandard */', '/* testMixedCase */'] as $comment) {
            $token = $this->getTargetToken($comment, T_FN);
            $this->backfillHelper($token);

            $this->assertSame($tokens[$token]['scope_opener'], ($token + 5), 'Scope opener is not the arrow token');
            $this->assertSame($tokens[$token]['scope_closer'], ($token + 12), 'Scope closer is not the semicolon token');

            $opener = $tokens[$token]['scope_opener'];
            $this->assertSame($tokens[$opener]['scope_opener'], ($token + 5), 'Opener scope opener is not the arrow token');
            $this->assertSame($tokens[$opener]['scope_closer'], ($token + 12), 'Opener scope closer is not the semicolon token');

            $closer = $tokens[$token]['scope_closer'];
            $this->assertSame($tokens[$closer]['scope_opener'], ($token + 5), 'Closer scope opener is not the arrow token');
            $this->assertSame($tokens[$closer]['scope_closer'], ($token + 12), 'Closer scope closer is not the semicolon token');
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
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testWhitespace */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 6), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 13), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 6), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 13), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 6), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 13), 'Closer scope closer is not the semicolon token');

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
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testComment */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 8), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 15), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 8), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 15), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 8), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 15), 'Closer scope closer is not the semicolon token');

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
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testHeredoc */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 4), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 9), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 4), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 9), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 4), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 9), 'Closer scope closer is not the semicolon token');

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
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testNestedOuter */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 5), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 25), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 5), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 25), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 5), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 25), 'Closer scope closer is not the semicolon token');

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

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 5), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 16), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 5), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 16), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token - 4), 'Closer scope opener is not the arrow token of the "outer" arrow function (shared scope closer)');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 16), 'Closer scope closer is not the semicolon token');

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
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testFunctionCall */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 5), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 17), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 5), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 17), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 5), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 17), 'Closer scope closer is not the semicolon token');

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
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testChainedFunctionCall */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 5), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 12), 'Scope closer is not the bracket token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 5), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 12), 'Opener scope closer is not the bracket token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 5), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 12), 'Closer scope closer is not the bracket token');

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
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testFunctionArgument */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 8), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 15), 'Scope closer is not the comma token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 8), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 15), 'Opener scope closer is not the comma token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 8), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 15), 'Closer scope closer is not the comma token');

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
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testClosure */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 5), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 60), 'Scope closer is not the comma token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 5), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 60), 'Opener scope closer is not the comma token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 5), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 60), 'Closer scope closer is not the comma token');

    }//end testClosure()


    /**
     * Test arrow functions with a return type.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testReturnType()
    {
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testReturnType */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 11), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 18), 'Scope closer is not the comma token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 11), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 18), 'Opener scope closer is not the comma token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 11), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 18), 'Closer scope closer is not the comma token');

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
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testReference */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 6), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 9), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 6), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 9), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 6), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 9), 'Closer scope closer is not the semicolon token');

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
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testGrouped */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 5), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 8), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 5), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 8), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 5), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 8), 'Closer scope closer is not the semicolon token');

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
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testArrayValue */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 4), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 9), 'Scope closer is not the comma token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 4), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 9), 'Opener scope closer is not the comma token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 4), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 9), 'Closer scope closer is not the comma token');

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
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testYield */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 5), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 14), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 5), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 14), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 5), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 14), 'Closer scope closer is not the semicolon token');

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
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testNullableNamespace */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 15), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 18), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 15), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 18), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 15), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 18), 'Closer scope closer is not the semicolon token');

    }//end testNullableNamespace()


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

            $this->assertSame($tokens[$token]['scope_opener'], ($token + 11), "Scope opener is not the arrow token (for $marker)");
            $this->assertSame($tokens[$token]['scope_closer'], ($token + 14), "Scope closer is not the semicolon token(for $marker)");

            $opener = $tokens[$token]['scope_opener'];
            $this->assertSame($tokens[$opener]['scope_opener'], ($token + 11), "Opener scope opener is not the arrow token(for $marker)");
            $this->assertSame($tokens[$opener]['scope_closer'], ($token + 14), "Opener scope closer is not the semicolon token(for $marker)");

            $closer = $tokens[$token]['scope_closer'];
            $this->assertSame($tokens[$closer]['scope_opener'], ($token + 11), "Closer scope opener is not the arrow token(for $marker)");
            $this->assertSame($tokens[$closer]['scope_closer'], ($token + 14), "Closer scope closer is not the semicolon token(for $marker)");
        }

    }//end testKeywordReturnTypes()


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

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 5), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 40), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 5), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 40), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 5), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 40), 'Closer scope closer is not the semicolon token');

        $token = $this->getTargetToken('/* testTernaryThen */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 8), 'Scope opener for THEN is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 12), 'Scope closer for THEN is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 8), 'Opener scope opener for THEN is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 12), 'Opener scope closer for THEN is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 8), 'Closer scope opener for THEN is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 12), 'Closer scope closer for THEN is not the semicolon token');

        $token = $this->getTargetToken('/* testTernaryElse */', T_FN);
        $this->backfillHelper($token, true);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 8), 'Scope opener for ELSE is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 11), 'Scope closer for ELSE is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 8), 'Opener scope opener for ELSE is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 11), 'Opener scope closer for ELSE is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token - 24), 'Closer scope opener for ELSE is not the arrow token of the "outer" arrow function (shared scope closer)');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 11), 'Closer scope closer for ELSE is not the semicolon token');

    }//end testTernary()


    /**
     * Test arrow function nested within a method declaration.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNestedInMethod()
    {
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testNestedInMethod */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 5), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 17), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 5), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 17), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_closer'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 5), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 17), 'Closer scope closer is not the semicolon token');

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
            ['/* testLiveCoding */'],
        ];

    }//end dataNotAnArrowFunction()


    /**
     * Helper function to check that all token keys are correctly set for T_FN tokens.
     *
     * @param string $token                The T_FN token to check.
     * @param bool   $skipScopeCloserCheck Whether to skip the scope closer check.
     *                                     This should be set to "true" when testing nested arrow functions,
     *                                     where the "inner" arrow function shares a scope closer with the
     *                                     "outer" arrow function, as the 'scope_condition' for the scope closer
     *                                     of the "inner" arrow function will point to the "outer" arrow function.
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


}//end class
