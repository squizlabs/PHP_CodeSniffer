<?php
/**
 * Tests the adding of the "parenthesis" keys to closure use tokens.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class UseParenthesisOwnerTest extends AbstractMethodUnitTest
{


    /**
     * Test that a non-closure use token does not get assigned the parenthesis_... indexes.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataUseNotClosure
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createTokenMap
     *
     * @return void
     */
    public function testUseNotClosure($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $use = $this->getTargetToken($testMarker, T_USE);
        $this->assertArrayNotHasKey('parenthesis_owner', $tokens[$use]);
        $this->assertArrayNotHasKey('parenthesis_opener', $tokens[$use]);
        $this->assertArrayNotHasKey('parenthesis_closer', $tokens[$use]);

    }//end testUseNotClosure()


    /**
     * Test that the next open/close parenthesis after a non-closure use token
     * do not get assigned the use keyword as a parenthesis owner.
     *
     * @param string   $testMarker        The comment which prefaces the target token in the test file.
     * @param int|null $expectedOwnerCode Optional. If an owner is expected for the parentheses, the token
     *                                    constant with is expected as the 'code'.
     *                                    If left at the default (null), the parentheses will be tested to
     *                                    *not* have an owner.
     *
     * @dataProvider dataUseNotClosure
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createTokenMap
     *
     * @return void
     */
    public function testUseNotClosureNextOpenClose($testMarker, $expectedOwnerCode=null)
    {
        $tokens = self::$phpcsFile->getTokens();
        $opener = $this->getTargetToken($testMarker, T_OPEN_PARENTHESIS);
        $closer = $this->getTargetToken($testMarker, T_CLOSE_PARENTHESIS);

        $this->assertArrayHasKey('parenthesis_opener', $tokens[$opener]);
        $this->assertArrayHasKey('parenthesis_closer', $tokens[$opener]);
        $this->assertSame($opener, $tokens[$opener]['parenthesis_opener']);
        $this->assertSame($closer, $tokens[$opener]['parenthesis_closer']);

        $this->assertArrayHasKey('parenthesis_opener', $tokens[$closer]);
        $this->assertArrayHasKey('parenthesis_closer', $tokens[$closer]);
        $this->assertSame($opener, $tokens[$closer]['parenthesis_opener']);
        $this->assertSame($closer, $tokens[$closer]['parenthesis_closer']);

        if ($expectedOwnerCode === null) {
            $this->assertArrayNotHasKey('parenthesis_owner', $tokens[$opener]);
            $this->assertArrayNotHasKey('parenthesis_owner', $tokens[$closer]);
        } else {
            $this->assertArrayHasKey('parenthesis_owner', $tokens[$opener]);
            $this->assertArrayHasKey('parenthesis_owner', $tokens[$closer]);
            $this->assertSame($expectedOwnerCode, $tokens[$tokens[$opener]['parenthesis_owner']]['code']);
            $this->assertSame($expectedOwnerCode, $tokens[$tokens[$closer]['parenthesis_owner']]['code']);
        }

    }//end testUseNotClosureNextOpenClose()


    /**
     * Data provider.
     *
     * @see testUseNotClosure()
     * @see testUseNotClosureNextOpenClose()
     *
     * @return array
     */
    public function dataUseNotClosure()
    {
        return [
            ['/* testUseImportSimple */'],
            ['/* testUseImportGroup */'],
            [
                '/* testUseTrait */',
                T_FUNCTION,
            ],
            ['/* testUseTraitInNestedAnonClass */'],
        ];

    }//end dataUseNotClosure()


    /**
     * Test that a closure use token gets assigned a parenthesis owner, opener and closer;
     * and that the opener/closer get the closure use token assigned as owner.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataClosureUse
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createTokenMap
     *
     * @return void
     */
    public function testClosureUse($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();
        $use    = $this->getTargetToken($testMarker, T_USE);
        $opener = $this->getTargetToken($testMarker, T_OPEN_PARENTHESIS);
        $closer = $this->getTargetToken($testMarker, T_CLOSE_PARENTHESIS);

        $this->assertArrayHasKey('parenthesis_owner', $tokens[$use]);
        $this->assertArrayHasKey('parenthesis_opener', $tokens[$use]);
        $this->assertArrayHasKey('parenthesis_closer', $tokens[$use]);
        $this->assertSame($use, $tokens[$use]['parenthesis_owner']);
        $this->assertSame($opener, $tokens[$use]['parenthesis_opener']);
        $this->assertSame($closer, $tokens[$use]['parenthesis_closer']);

        $this->assertArrayHasKey('parenthesis_owner', $tokens[$opener]);
        $this->assertArrayHasKey('parenthesis_opener', $tokens[$opener]);
        $this->assertArrayHasKey('parenthesis_closer', $tokens[$opener]);
        $this->assertSame($use, $tokens[$opener]['parenthesis_owner']);
        $this->assertSame($opener, $tokens[$opener]['parenthesis_opener']);
        $this->assertSame($closer, $tokens[$opener]['parenthesis_closer']);

        $this->assertArrayHasKey('parenthesis_owner', $tokens[$closer]);
        $this->assertArrayHasKey('parenthesis_opener', $tokens[$closer]);
        $this->assertArrayHasKey('parenthesis_closer', $tokens[$closer]);
        $this->assertSame($use, $tokens[$closer]['parenthesis_owner']);
        $this->assertSame($opener, $tokens[$closer]['parenthesis_opener']);
        $this->assertSame($closer, $tokens[$closer]['parenthesis_closer']);

    }//end testClosureUse()


    /**
     * Data provider.
     *
     * @see testClosureUse()
     *
     * @return array
     */
    public function dataClosureUse()
    {
        return [
            ['/* testClosureUse */'],
            ['/* testClosureUseNestedInClass */'],
        ];

    }//end dataClosureUse()


    /**
     * Test (and document) the behaviour of the parentheses setting during live coding, when a
     * `use` token is encountered at the very end of a file.
     *
     * @covers PHP_CodeSniffer\Tokenizers\Tokenizer::createTokenMap
     *
     * @return void
     */
    public function testLiveCoding()
    {
        $tokens = self::$phpcsFile->getTokens();
        $use    = $this->getTargetToken('/* testLiveCoding */', T_USE);

        $this->assertArrayHasKey('parenthesis_owner', $tokens[$use]);
        $this->assertArrayHasKey('parenthesis_opener', $tokens[$use]);
        $this->assertArrayHasKey('parenthesis_closer', $tokens[$use]);
        $this->assertSame($use, $tokens[$use]['parenthesis_owner']);
        $this->assertNull($tokens[$use]['parenthesis_opener']);
        $this->assertNull($tokens[$use]['parenthesis_closer']);

    }//end testLiveCoding()


}//end class
