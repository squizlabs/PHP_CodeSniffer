<?php
/**
 * Tests the adding of the "bracket_opener/closer" keys to use group tokens.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class ScopeSettingWithNamespaceOperatorTest extends AbstractMethodUnitTest
{


    /**
     * Test that the scope opener/closers are set correctly when the namespace keyword is encountered as an operator.
     *
     * @param string       $testMarker The comment which prefaces the target tokens in the test file.
     * @param int|string[] $tokenTypes The token type to search for.
     * @param int|string[] $open       Optional. The token type for the scope opener.
     * @param int|string[] $close      Optional. The token type for the scope closer.
     *
     * @dataProvider dataScopeSetting
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testScopeSetting($testMarker, $tokenTypes, $open=T_OPEN_CURLY_BRACKET, $close=T_CLOSE_CURLY_BRACKET)
    {
        $tokens = self::$phpcsFile->getTokens();

        $target = $this->getTargetToken($testMarker, $tokenTypes);
        $opener = $this->getTargetToken($testMarker, $open);
        $closer = $this->getTargetToken($testMarker, $close);

        $this->assertArrayHasKey('scope_opener', $tokens[$target], 'Scope opener missing');
        $this->assertArrayHasKey('scope_closer', $tokens[$target], 'Scope closer missing');
        $this->assertSame($opener, $tokens[$target]['scope_opener'], 'Scope opener not same');
        $this->assertSame($closer, $tokens[$target]['scope_closer'], 'Scope closer not same');

        $this->assertArrayHasKey('scope_opener', $tokens[$opener], 'Scope opener missing for open curly');
        $this->assertArrayHasKey('scope_closer', $tokens[$opener], 'Scope closer missing for open curly');
        $this->assertSame($opener, $tokens[$opener]['scope_opener'], 'Scope opener not same for open curly');
        $this->assertSame($closer, $tokens[$opener]['scope_closer'], 'Scope closer not same for open curly');

        $this->assertArrayHasKey('scope_opener', $tokens[$closer], 'Scope opener missing for close curly');
        $this->assertArrayHasKey('scope_closer', $tokens[$closer], 'Scope closer missing for close curly');
        $this->assertSame($opener, $tokens[$closer]['scope_opener'], 'Scope opener not same for close curly');
        $this->assertSame($closer, $tokens[$closer]['scope_closer'], 'Scope closer not same for close curly');

    }//end testScopeSetting()


    /**
     * Data provider.
     *
     * @see testScopeSetting()
     *
     * @return array
     */
    public function dataScopeSetting()
    {
        return [
            [
                '/* testClassExtends */',
                [T_CLASS],
            ],
            [
                '/* testClassImplements */',
                [T_ANON_CLASS],
            ],
            [
                '/* testInterfaceExtends */',
                [T_INTERFACE],
            ],
            [
                '/* testFunctionReturnType */',
                [T_FUNCTION],
            ],
            [
                '/* testClosureReturnType */',
                [T_CLOSURE],
            ],
            [
                '/* testArrowFunctionReturnType */',
                [T_FN],
                [T_FN_ARROW],
                [T_SEMICOLON],
            ],
        ];

    }//end dataScopeSetting()


}//end class
