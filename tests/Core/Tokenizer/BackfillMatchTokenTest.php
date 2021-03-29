<?php
/**
 * Tests the backfilling of the T_MATCH token to PHP < 8.0, as well as the
 * setting of parenthesis/scopes for match control structures across PHP versions.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020-2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Tokens;

class BackfillMatchTokenTest extends AbstractMethodUnitTest
{


    /**
     * Test tokenization of match expressions.
     *
     * @param string $testMarker   The comment prefacing the target token.
     * @param int    $openerOffset The expected offset of the scope opener in relation to the testMarker.
     * @param int    $closerOffset The expected offset of the scope closer in relation to the testMarker.
     * @param string $testContent  The token content to look for.
     *
     * @dataProvider dataMatchExpression
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testMatchExpression($testMarker, $openerOffset, $closerOffset, $testContent='match')
    {
        $tokens = self::$phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_STRING, T_MATCH], $testContent);
        $tokenArray = $tokens[$token];

        $this->assertSame(T_MATCH, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_MATCH (code)');
        $this->assertSame('T_MATCH', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_MATCH (type)');

        $this->scopeTestHelper($token, $openerOffset, $closerOffset);
        $this->parenthesisTestHelper($token);

    }//end testMatchExpression()


    /**
     * Data provider.
     *
     * @see testMatchExpression()
     *
     * @return array
     */
    public function dataMatchExpression()
    {
        return [
            'simple_match'                              => [
                '/* testMatchSimple */',
                6,
                33,
            ],
            'no_trailing_comma'                         => [
                '/* testMatchNoTrailingComma */',
                6,
                24,
            ],
            'with_default_case'                         => [
                '/* testMatchWithDefault */',
                6,
                33,
            ],
            'expression_in_condition'                   => [
                '/* testMatchExpressionInCondition */',
                6,
                77,
            ],
            'multicase'                                 => [
                '/* testMatchMultiCase */',
                6,
                40,
            ],
            'multicase_trailing_comma_in_case'          => [
                '/* testMatchMultiCaseTrailingCommaInCase */',
                6,
                47,
            ],
            'in_closure_not_lowercase'                  => [
                '/* testMatchInClosureNotLowercase */',
                6,
                36,
                'Match',
            ],
            'in_arrow_function'                         => [
                '/* testMatchInArrowFunction */',
                5,
                36,
            ],
            'arrow_function_in_match_no_trailing_comma' => [
                '/* testArrowFunctionInMatchNoTrailingComma */',
                6,
                44,
            ],
            'in_function_call_param_not_lowercase'      => [
                '/* testMatchInFunctionCallParamNotLowercase */',
                8,
                32,
                'MATCH',
            ],
            'in_method_call_param'                      => [
                '/* testMatchInMethodCallParam */',
                5,
                13,
            ],
            'discard_result'                            => [
                '/* testMatchDiscardResult */',
                6,
                18,
            ],
            'duplicate_conditions_and_comments'         => [
                '/* testMatchWithDuplicateConditionsWithComments */',
                12,
                59,
            ],
            'nested_match_outer'                        => [
                '/* testNestedMatchOuter */',
                6,
                33,
            ],
            'nested_match_inner'                        => [
                '/* testNestedMatchInner */',
                6,
                14,
            ],
            'ternary_condition'                         => [
                '/* testMatchInTernaryCondition */',
                6,
                21,
            ],
            'ternary_then'                              => [
                '/* testMatchInTernaryThen */',
                6,
                21,
            ],
            'ternary_else'                              => [
                '/* testMatchInTernaryElse */',
                6,
                21,
            ],
            'array_value'                               => [
                '/* testMatchInArrayValue */',
                6,
                21,
            ],
            'array_key'                                 => [
                '/* testMatchInArrayKey */',
                6,
                21,
            ],
            'returning_array'                           => [
                '/* testMatchreturningArray */',
                6,
                125,
            ],
            'nested_in_switch_case_1'                   => [
                '/* testMatchWithDefaultNestedInSwitchCase1 */',
                6,
                25,
            ],
            'nested_in_switch_case_2'                   => [
                '/* testMatchWithDefaultNestedInSwitchCase2 */',
                6,
                25,
            ],
            'nested_in_switch_default'                  => [
                '/* testMatchWithDefaultNestedInSwitchDefault */',
                6,
                25,
            ],
            'match_with_nested_switch'                  => [
                '/* testMatchContainingSwitch */',
                6,
                180,
            ],
            'no_cases'                                  => [
                '/* testMatchNoCases */',
                6,
                7,
            ],
            'multi_default'                             => [
                '/* testMatchMultiDefault */',
                6,
                40,
            ],
        ];

    }//end dataMatchExpression()


    /**
     * Verify that "match" keywords which are not match control structures get tokenized as T_STRING
     * and don't have the extra token array indexes.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to look for.
     *
     * @dataProvider dataNotAMatchStructure
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testNotAMatchStructure($testMarker, $testContent='match')
    {
        $tokens = self::$phpcsFile->getTokens();

        $token      = $this->getTargetToken($testMarker, [T_STRING, T_MATCH], $testContent);
        $tokenArray = $tokens[$token];

        $this->assertSame(T_STRING, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (code)');
        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (type)');

        $this->assertArrayNotHasKey('scope_condition', $tokenArray, 'Scope condition is set');
        $this->assertArrayNotHasKey('scope_opener', $tokenArray, 'Scope opener is set');
        $this->assertArrayNotHasKey('scope_closer', $tokenArray, 'Scope closer is set');
        $this->assertArrayNotHasKey('parenthesis_owner', $tokenArray, 'Parenthesis owner is set');
        $this->assertArrayNotHasKey('parenthesis_opener', $tokenArray, 'Parenthesis opener is set');
        $this->assertArrayNotHasKey('parenthesis_closer', $tokenArray, 'Parenthesis closer is set');

        $next = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($token + 1), null, true);
        if ($next !== false && $tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            $this->assertArrayNotHasKey('parenthesis_owner', $tokenArray, 'Parenthesis owner is set for opener after');
        }

    }//end testNotAMatchStructure()


    /**
     * Data provider.
     *
     * @see testNotAMatchStructure()
     *
     * @return array
     */
    public function dataNotAMatchStructure()
    {
        return [
            'static_method_call'                   => ['/* testNoMatchStaticMethodCall */'],
            'class_constant_access'                => [
                '/* testNoMatchClassConstantAccess */',
                'MATCH',
            ],
            'class_constant_array_access'          => [
                '/* testNoMatchClassConstantArrayAccessMixedCase */',
                'Match',
            ],
            'method_call'                          => ['/* testNoMatchMethodCall */'],
            'method_call_uppercase'                => [
                '/* testNoMatchMethodCallUpper */',
                'MATCH',
            ],
            'property_access'                      => ['/* testNoMatchPropertyAccess */'],
            'namespaced_function_call'             => ['/* testNoMatchNamespacedFunctionCall */'],
            'namespace_operator_function_call'     => ['/* testNoMatchNamespaceOperatorFunctionCall */'],
            'interface_method_declaration'         => ['/* testNoMatchInterfaceMethodDeclaration */'],
            'class_constant_declaration'           => ['/* testNoMatchClassConstantDeclarationLower */'],
            'class_method_declaration'             => ['/* testNoMatchClassMethodDeclaration */'],
            'property_assigment'                   => ['/* testNoMatchPropertyAssignment */'],
            'class_instantiation'                  => [
                '/* testNoMatchClassInstantiation */',
                'Match',
            ],
            'anon_class_method_declaration'        => [
                '/* testNoMatchAnonClassMethodDeclaration */',
                'maTCH',
            ],
            'class_declaration'                    => [
                '/* testNoMatchClassDeclaration */',
                'Match',
            ],
            'interface_declaration'                => [
                '/* testNoMatchInterfaceDeclaration */',
                'Match',
            ],
            'trait_declaration'                    => [
                '/* testNoMatchTraitDeclaration */',
                'Match',
            ],
            'constant_declaration'                 => [
                '/* testNoMatchConstantDeclaration */',
                'MATCH',
            ],
            'function_declaration'                 => ['/* testNoMatchFunctionDeclaration */'],
            'namespace_declaration'                => [
                '/* testNoMatchNamespaceDeclaration */',
                'Match',
            ],
            'class_extends_declaration'            => [
                '/* testNoMatchExtendedClassDeclaration */',
                'Match',
            ],
            'class_implements_declaration'         => [
                '/* testNoMatchImplementedClassDeclaration */',
                'Match',
            ],
            'use_statement'                        => [
                '/* testNoMatchInUseStatement */',
                'Match',
            ],
            'unsupported_inline_control_structure' => ['/* testNoMatchMissingCurlies */'],
            'unsupported_alternative_syntax'       => ['/* testNoMatchAlternativeSyntax */'],
            'live_coding'                          => ['/* testLiveCoding */'],
        ];

    }//end dataNotAMatchStructure()


    /**
     * Verify that the tokenization of switch structures is not affected by the backfill.
     *
     * @param string $testMarker   The comment prefacing the target token.
     * @param int    $openerOffset The expected offset of the scope opener in relation to the testMarker.
     * @param int    $closerOffset The expected offset of the scope closer in relation to the testMarker.
     *
     * @dataProvider dataSwitchExpression
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testSwitchExpression($testMarker, $openerOffset, $closerOffset)
    {
        $token = $this->getTargetToken($testMarker, T_SWITCH);

        $this->scopeTestHelper($token, $openerOffset, $closerOffset);
        $this->parenthesisTestHelper($token);

    }//end testSwitchExpression()


    /**
     * Data provider.
     *
     * @see testSwitchExpression()
     *
     * @return array
     */
    public function dataSwitchExpression()
    {
        return [
            'switch_containing_match'   => [
                '/* testSwitchContainingMatch */',
                6,
                174,
            ],
            'match_containing_switch_1' => [
                '/* testSwitchNestedInMatch1 */',
                5,
                63,
            ],
            'match_containing_switch_2' => [
                '/* testSwitchNestedInMatch2 */',
                5,
                63,
            ],
        ];

    }//end dataSwitchExpression()


    /**
     * Verify that the tokenization of a switch case/default structure containing a match structure
     * or contained *in* a match structure is not affected by the backfill.
     *
     * @param string $testMarker   The comment prefacing the target token.
     * @param int    $openerOffset The expected offset of the scope opener in relation to the testMarker.
     * @param int    $closerOffset The expected offset of the scope closer in relation to the testMarker.
     *
     * @dataProvider dataSwitchCaseVersusMatch
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testSwitchCaseVersusMatch($testMarker, $openerOffset, $closerOffset)
    {
        $token = $this->getTargetToken($testMarker, [T_CASE, T_DEFAULT]);

        $this->scopeTestHelper($token, $openerOffset, $closerOffset);

    }//end testSwitchCaseVersusMatch()


    /**
     * Data provider.
     *
     * @see testSwitchCaseVersusMatch()
     *
     * @return array
     */
    public function dataSwitchCaseVersusMatch()
    {
        return [
            'switch_with_nested_match_case_1'       => [
                '/* testMatchWithDefaultNestedInSwitchCase1 */',
                3,
                55,
            ],
            'switch_with_nested_match_case_2'       => [
                '/* testMatchWithDefaultNestedInSwitchCase2 */',
                4,
                21,
            ],
            'switch_with_nested_match_default_case' => [
                '/* testMatchWithDefaultNestedInSwitchDefault */',
                1,
                38,
            ],
            'match_with_nested_switch_case'         => [
                '/* testSwitchDefaultNestedInMatchCase */',
                1,
                18,
            ],
            'match_with_nested_switch_default_case' => [
                '/* testSwitchDefaultNestedInMatchDefault */',
                1,
                20,
            ],
        ];

    }//end dataSwitchCaseVersusMatch()


    /**
     * Helper function to verify that all scope related array indexes for a control structure
     * are set correctly.
     *
     * @param string $token                The control structure token to check.
     * @param int    $openerOffset         The expected offset of the scope opener in relation to
     *                                     the control structure token.
     * @param int    $closerOffset         The expected offset of the scope closer in relation to
     *                                     the control structure token.
     * @param bool   $skipScopeCloserCheck Whether to skip the scope closer check.
     *                                     This should be set to "true" when testing nested arrow functions,
     *                                     where the "inner" arrow function shares a scope closer with the
     *                                     "outer" arrow function, as the 'scope_condition' for the scope closer
     *                                     of the "inner" arrow function will point to the "outer" arrow function.
     *
     * @return void
     */
    private function scopeTestHelper($token, $openerOffset, $closerOffset, $skipScopeCloserCheck=false)
    {
        $tokens     = self::$phpcsFile->getTokens();
        $tokenArray = $tokens[$token];
        $tokenType  = $tokenArray['type'];
        $expectedScopeOpener = ($token + $openerOffset);
        $expectedScopeCloser = ($token + $closerOffset);

        $this->assertArrayHasKey('scope_condition', $tokenArray, 'Scope condition is not set');
        $this->assertArrayHasKey('scope_opener', $tokenArray, 'Scope opener is not set');
        $this->assertArrayHasKey('scope_closer', $tokenArray, 'Scope closer is not set');
        $this->assertSame($token, $tokenArray['scope_condition'], 'Scope condition is not the '.$tokenType.' token');
        $this->assertSame($expectedScopeOpener, $tokenArray['scope_opener'], 'Scope opener of the '.$tokenType.' token incorrect');
        $this->assertSame($expectedScopeCloser, $tokenArray['scope_closer'], 'Scope closer of the '.$tokenType.' token incorrect');

        $opener = $tokenArray['scope_opener'];
        $this->assertArrayHasKey('scope_condition', $tokens[$opener], 'Opener scope condition is not set');
        $this->assertArrayHasKey('scope_opener', $tokens[$opener], 'Opener scope opener is not set');
        $this->assertArrayHasKey('scope_closer', $tokens[$opener], 'Opener scope closer is not set');
        $this->assertSame($token, $tokens[$opener]['scope_condition'], 'Opener scope condition is not the '.$tokenType.' token');
        $this->assertSame($expectedScopeOpener, $tokens[$opener]['scope_opener'], $tokenType.' opener scope opener token incorrect');
        $this->assertSame($expectedScopeCloser, $tokens[$opener]['scope_closer'], $tokenType.' opener scope closer token incorrect');

        $closer = $tokenArray['scope_closer'];
        $this->assertArrayHasKey('scope_condition', $tokens[$closer], 'Closer scope condition is not set');
        $this->assertArrayHasKey('scope_opener', $tokens[$closer], 'Closer scope opener is not set');
        $this->assertArrayHasKey('scope_closer', $tokens[$closer], 'Closer scope closer is not set');
        if ($skipScopeCloserCheck === false) {
            $this->assertSame($token, $tokens[$closer]['scope_condition'], 'Closer scope condition is not the '.$tokenType.' token');
        }

        $this->assertSame($expectedScopeOpener, $tokens[$closer]['scope_opener'], $tokenType.' closer scope opener token incorrect');
        $this->assertSame($expectedScopeCloser, $tokens[$closer]['scope_closer'], $tokenType.' closer scope closer token incorrect');

        if (($opener + 1) !== $closer) {
            for ($i = ($opener + 1); $i < $closer; $i++) {
                $this->assertArrayHasKey(
                    $token,
                    $tokens[$i]['conditions'],
                    $tokenType.' condition not added for token belonging to the '.$tokenType.' structure'
                );
            }
        }

    }//end scopeTestHelper()


    /**
     * Helper function to verify that all parenthesis related array indexes for a control structure
     * token are set correctly.
     *
     * @param int $token The position of the control structure token.
     *
     * @return void
     */
    private function parenthesisTestHelper($token)
    {
        $tokens     = self::$phpcsFile->getTokens();
        $tokenArray = $tokens[$token];
        $tokenType  = $tokenArray['type'];

        $this->assertArrayHasKey('parenthesis_owner', $tokenArray, 'Parenthesis owner is not set');
        $this->assertArrayHasKey('parenthesis_opener', $tokenArray, 'Parenthesis opener is not set');
        $this->assertArrayHasKey('parenthesis_closer', $tokenArray, 'Parenthesis closer is not set');
        $this->assertSame($token, $tokenArray['parenthesis_owner'], 'Parenthesis owner is not the '.$tokenType.' token');

        $opener = $tokenArray['parenthesis_opener'];
        $this->assertArrayHasKey('parenthesis_owner', $tokens[$opener], 'Opening parenthesis owner is not set');
        $this->assertSame($token, $tokens[$opener]['parenthesis_owner'], 'Opening parenthesis owner is not the '.$tokenType.' token');

        $closer = $tokenArray['parenthesis_closer'];
        $this->assertArrayHasKey('parenthesis_owner', $tokens[$closer], 'Closing parenthesis owner is not set');
        $this->assertSame($token, $tokens[$closer]['parenthesis_owner'], 'Closing parenthesis owner is not the '.$tokenType.' token');

    }//end parenthesisTestHelper()


}//end class
