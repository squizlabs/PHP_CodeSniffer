<?php
/**
 * Tests the adding of the "scope_opener/closer" keys to use group tokens.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class UseGroupScopeIndexesTest extends AbstractMethodUnitTest
{


    /**
     * Test that use group tokens are assigned scope_opener/scope_closer indexes.
     *
     * @param string $testMarker The comment which prefaces the target tokens in the test file.
     *
     * @dataProvider dataUseGroupScopeIndexes
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createTokenMap
     *
     * @return void
     */
    public function testUseGroupScopeIndexes($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $use    = $this->getTargetToken($testMarker, T_USE);
        $opener = $this->getTargetToken($testMarker, T_OPEN_USE_GROUP);
        $closer = $this->getTargetToken($testMarker, T_CLOSE_USE_GROUP);

        $this->assertArrayHasKey('scope_opener', $tokens[$use], 'Scope opener missing for use keyword');
        $this->assertArrayHasKey('scope_closer', $tokens[$use], 'Scope closer missing for use keyword');
        $this->assertSame($opener, $tokens[$use]['scope_opener'], 'Scope opener not same for use keyword');
        $this->assertSame($closer, $tokens[$use]['scope_closer'], 'Scope closer not same for use keyword');

        $this->assertArrayHasKey('scope_opener', $tokens[$opener], 'Scope opener missing for group use open curly');
        $this->assertArrayHasKey('scope_closer', $tokens[$opener], 'Scope closer missing for group use open curly');
        $this->assertSame($opener, $tokens[$opener]['scope_opener'], 'Scope opener not same for group use open curly');
        $this->assertSame($closer, $tokens[$opener]['scope_closer'], 'Scope closer not same for group use open curly');

        $this->assertArrayHasKey('scope_opener', $tokens[$closer], 'Scope opener missing for group use close curly');
        $this->assertArrayHasKey('scope_closer', $tokens[$closer], 'Scope closer missing for group use close curly');
        $this->assertSame($opener, $tokens[$closer]['scope_opener'], 'Scope opener not same for group use close curly');
        $this->assertSame($closer, $tokens[$closer]['scope_closer'], 'Scope closer not same for group use close curly');

    }//end testUseGroupScopeIndexes()


    /**
     * Data provider.
     *
     * @see testUseGroupScopeIndexes()
     *
     * @return array
     */
    public function dataUseGroupScopeIndexes()
    {
        return [
            ['/* testUseGroupSingleLine */'],
            ['/* testUseGroupMultiLineWithTrailingComma */'],
        ];

    }//end dataUseGroupScopeIndexes()


}//end class
