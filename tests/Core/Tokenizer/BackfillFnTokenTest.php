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

            $closer = $tokens[$token]['scope_opener'];
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

        $closer = $tokens[$token]['scope_opener'];
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
    public function testComments()
    {
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testComment */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 8), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 15), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 8), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 15), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 8), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 15), 'Closer scope closer is not the semicolon token');

    }//end testComments()


    /**
     * Test a function called fn.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testFunctionName()
    {
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testFunctionName */', T_FN);
        $this->assertFalse(array_key_exists('scope_condition', $tokens[$token]), 'Scope condition is set');
        $this->assertFalse(array_key_exists('scope_opener', $tokens[$token]), 'Scope opener is set');
        $this->assertFalse(array_key_exists('scope_closer', $tokens[$token]), 'Scope closer is set');
        $this->assertFalse(array_key_exists('parenthesis_owner', $tokens[$token]), 'Parenthesis owner is set');
        $this->assertFalse(array_key_exists('parenthesis_opener', $tokens[$token]), 'Parenthesis opener is set');
        $this->assertFalse(array_key_exists('parenthesis_closer', $tokens[$token]), 'Parenthesis closer is set');

    }//end testFunctionName()


    /**
     * Test nested arrow functions.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNested()
    {
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken('/* testNested */', T_FN);
        $this->backfillHelper($token);

        $this->assertSame($tokens[$token]['scope_opener'], ($token + 5), 'Scope opener is not the arrow token');
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 23), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 5), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 23), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 5), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 23), 'Closer scope closer is not the semicolon token');

    }//end testNested()


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

        $closer = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 5), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 17), 'Closer scope closer is not the semicolon token');

    }//end testFunctionCall()


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
        $this->assertSame($tokens[$token]['scope_closer'], ($token + 60), 'Scope closer is not the semicolon token');

        $opener = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$opener]['scope_opener'], ($token + 5), 'Opener scope opener is not the arrow token');
        $this->assertSame($tokens[$opener]['scope_closer'], ($token + 60), 'Opener scope closer is not the semicolon token');

        $closer = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 5), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 60), 'Closer scope closer is not the semicolon token');

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

        $closer = $tokens[$token]['scope_opener'];
        $this->assertSame($tokens[$closer]['scope_opener'], ($token + 11), 'Closer scope opener is not the arrow token');
        $this->assertSame($tokens[$closer]['scope_closer'], ($token + 18), 'Closer scope closer is not the comma token');

    }//end testReturnType()


    /**
     * Test that anonymous class tokens without parenthesis do not get assigned a parenthesis owner.
     *
     * @param string $token The T_FN token to check.
     *
     * @return void
     */
    private function backfillHelper($token)
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

        $closer = $tokens[$token]['scope_opener'];
        $this->assertTrue(array_key_exists('scope_condition', $tokens[$closer]), 'Closer scope condition is not set');
        $this->assertTrue(array_key_exists('scope_opener', $tokens[$closer]), 'Closer scope opener is not set');
        $this->assertTrue(array_key_exists('scope_closer', $tokens[$closer]), 'Closer scope closer is not set');
        $this->assertSame($tokens[$closer]['scope_condition'], $token, 'Closer scope condition is not the T_FN token');

        $opener = $tokens[$token]['parenthesis_opener'];
        $this->assertTrue(array_key_exists('parenthesis_owner', $tokens[$opener]), 'Opening parenthesis owner is not set');
        $this->assertSame($tokens[$opener]['parenthesis_owner'], $token, 'Opening parenthesis owner is not the T_FN token');

        $closer = $tokens[$token]['parenthesis_closer'];
        $this->assertTrue(array_key_exists('parenthesis_owner', $tokens[$closer]), 'Closing parenthesis owner is not set');
        $this->assertSame($tokens[$closer]['parenthesis_owner'], $token, 'Closing parenthesis owner is not the T_FN token');

    }//end backfillHelper()


}//end class
