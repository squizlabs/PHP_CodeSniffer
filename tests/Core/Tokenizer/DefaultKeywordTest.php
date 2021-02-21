<?php
/**
 * Tests the retokenization of the `default` keyword to T_MATCH_DEFAULT for PHP 8.0 match structures
 * and makes sure that the tokenization of switch `T_DEFAULT` structures is not aversely affected.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020-2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class DefaultKeywordTest extends AbstractMethodUnitTest
{


    /**
     * Test the retokenization of the `default` keyword for match structure to `T_MATCH_DEFAULT`.
     *
     * Note: Cases and default structures within a match structure do *NOT* get case/default scope
     * conditions, in contrast to case and default structures in switch control structures.
     *
     * @param string $testMarker The comment prefacing the target token.
     *
     * @dataProvider dataMatchDefault
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testMatchDefault($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_MATCH_DEFAULT, T_DEFAULT]);
        $tokenArray = $tokens[$token];

        $this->assertSame(T_MATCH_DEFAULT, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_MATCH_DEFAULT (code)');
        $this->assertSame('T_MATCH_DEFAULT', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_MATCH_DEFAULT (type)');

        $this->assertArrayNotHasKey('scope_condition', $tokenArray, 'Scope condition is set');
        $this->assertArrayNotHasKey('scope_opener', $tokenArray, 'Scope opener is set');
        $this->assertArrayNotHasKey('scope_closer', $tokenArray, 'Scope closer is set');

    }//end testMatchDefault()


    /**
     * Data provider.
     *
     * @see testMatchDefault()
     *
     * @return array
     */
    public function dataMatchDefault()
    {
        return [
            'simple_match_default'            => ['/* testSimpleMatchDefault */'],
            'match_default_in_switch_case_1'  => ['/* testMatchDefaultNestedInSwitchCase1 */'],
            'match_default_in_switch_case_2'  => ['/* testMatchDefaultNestedInSwitchCase2 */'],
            'match_default_in_switch_default' => ['/* testMatchDefaultNestedInSwitchDefault */'],
            'match_default_containing_switch' => ['/* testMatchDefault */'],
        ];

    }//end dataMatchDefault()


    /**
     * Verify that the retokenization of `T_DEFAULT` tokens in match constructs, doesn't negatively
     * impact the tokenization of `T_DEFAULT` tokens in switch control structures.
     *
     * Note: Cases and default structures within a switch control structure *do* get case/default scope
     * conditions.
     *
     * @param string   $testMarker    The comment prefacing the target token.
     * @param int      $openerOffset  The expected offset of the scope opener in relation to the testMarker.
     * @param int      $closerOffset  The expected offset of the scope closer in relation to the testMarker.
     * @param int|null $conditionStop The expected offset at which tokens stop having T_DEFAULT as a scope condition.
     *
     * @dataProvider dataSwitchDefault
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testSwitchDefault($testMarker, $openerOffset, $closerOffset, $conditionStop=null)
    {
        $tokens = self::$phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_MATCH_DEFAULT, T_DEFAULT]);
        $tokenArray = $tokens[$token];
        $expectedScopeOpener = ($token + $openerOffset);
        $expectedScopeCloser = ($token + $closerOffset);

        $this->assertSame(T_DEFAULT, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_DEFAULT (code)');
        $this->assertSame('T_DEFAULT', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_DEFAULT (type)');

        $this->assertArrayHasKey('scope_condition', $tokenArray, 'Scope condition is not set');
        $this->assertArrayHasKey('scope_opener', $tokenArray, 'Scope opener is not set');
        $this->assertArrayHasKey('scope_closer', $tokenArray, 'Scope closer is not set');
        $this->assertSame($token, $tokenArray['scope_condition'], 'Scope condition is not the T_DEFAULT token');
        $this->assertSame($expectedScopeOpener, $tokenArray['scope_opener'], 'Scope opener of the T_DEFAULT token incorrect');
        $this->assertSame($expectedScopeCloser, $tokenArray['scope_closer'], 'Scope closer of the T_DEFAULT token incorrect');

        $opener = $tokenArray['scope_opener'];
        $this->assertArrayHasKey('scope_condition', $tokens[$opener], 'Opener scope condition is not set');
        $this->assertArrayHasKey('scope_opener', $tokens[$opener], 'Opener scope opener is not set');
        $this->assertArrayHasKey('scope_closer', $tokens[$opener], 'Opener scope closer is not set');
        $this->assertSame($token, $tokens[$opener]['scope_condition'], 'Opener scope condition is not the T_DEFAULT token');
        $this->assertSame($expectedScopeOpener, $tokens[$opener]['scope_opener'], 'T_DEFAULT opener scope opener token incorrect');
        $this->assertSame($expectedScopeCloser, $tokens[$opener]['scope_closer'], 'T_DEFAULT opener scope closer token incorrect');

        $closer = $tokenArray['scope_closer'];
        $this->assertArrayHasKey('scope_condition', $tokens[$closer], 'Closer scope condition is not set');
        $this->assertArrayHasKey('scope_opener', $tokens[$closer], 'Closer scope opener is not set');
        $this->assertArrayHasKey('scope_closer', $tokens[$closer], 'Closer scope closer is not set');
        $this->assertSame($token, $tokens[$closer]['scope_condition'], 'Closer scope condition is not the T_DEFAULT token');
        $this->assertSame($expectedScopeOpener, $tokens[$closer]['scope_opener'], 'T_DEFAULT closer scope opener token incorrect');
        $this->assertSame($expectedScopeCloser, $tokens[$closer]['scope_closer'], 'T_DEFAULT closer scope closer token incorrect');

        if (($opener + 1) !== $closer) {
            $end = $closer;
            if (isset($conditionStop) === true) {
                $end = $conditionStop;
            }

            for ($i = ($opener + 1); $i < $end; $i++) {
                $this->assertArrayHasKey(
                    $token,
                    $tokens[$i]['conditions'],
                    'T_DEFAULT condition not added for token belonging to the T_DEFAULT structure'
                );
            }
        }

    }//end testSwitchDefault()


    /**
     * Data provider.
     *
     * @see testSwitchDefault()
     *
     * @return array
     */
    public function dataSwitchDefault()
    {
        return [
            'simple_switch_default'                  => [
                '/* testSimpleSwitchDefault */',
                1,
                4,
            ],
            'simple_switch_default_with_curlies'     => [
                // For a default structure with curly braces, the scope opener
                // will be the open curly and the closer the close curly.
                // However, scope conditions will not be set for open to close,
                // but only for the open token up to the "break/return/continue" etc.
                '/* testSimpleSwitchDefaultWithCurlies */',
                3,
                12,
                6,
            ],
            'switch_default_toplevel'                => [
                '/* testSwitchDefault */',
                1,
                43,
            ],
            'switch_default_nested_in_match_case'    => [
                '/* testSwitchDefaultNestedInMatchCase */',
                1,
                20,
            ],
            'switch_default_nested_in_match_default' => [
                '/* testSwitchDefaultNestedInMatchDefault */',
                1,
                18,
            ],
        ];

    }//end dataSwitchDefault()


}//end class
