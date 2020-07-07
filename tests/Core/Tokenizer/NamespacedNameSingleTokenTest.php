<?php
/**
 * Tests the tokenization of identifier names.
 *
 * As of PHP 8, identifier names are tokenized differently, depending on them being
 * either fully qualified, partially qualified or relative to the current namespace.
 *
 * This test file safeguards that in PHPCS 4.x this new form of tokenization is correctly
 * backfilled and that the tokenization of these identifier names is the same in all
 * PHP versions based on how these names are tokenized in PHP 8.
 *
 * {@link https://wiki.php.net/rfc/namespaced_names_as_token}
 * {@link https://github.com/squizlabs/PHP_CodeSniffer/issues/3041}
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Tokens;

class NamespacedNameSingleTokenTest extends AbstractMethodUnitTest
{


    /**
     * Test that identifier names are tokenized the same across PHP versions, based on the PHP 8 tokenization.
     *
     * @param string $testMarker     The comment prefacing the test.
     * @param array  $expectedTokens The tokenization expected.
     *
     * @dataProvider dataIdentifierTokenization
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testIdentifierTokenization($testMarker, $expectedTokens)
    {
        $tokens     = self::$phpcsFile->getTokens();
        $identifier = $this->getTargetToken($testMarker, constant($expectedTokens[0]['type']));

        foreach ($expectedTokens as $key => $tokenInfo) {
            $this->assertSame(
                constant($tokenInfo['type']),
                $tokens[$identifier]['code'],
                'Failed asserting that '.$tokens[$identifier]['type'].' is the same as the expected type: '.$tokenInfo['type']
            );
            $this->assertSame($tokenInfo['type'], $tokens[$identifier]['type']);
            $this->assertSame($tokenInfo['content'], $tokens[$identifier]['content']);

            ++$identifier;
        }

    }//end testIdentifierTokenization()


    /**
     * Data provider.
     *
     * @see testIdentifierTokenization()
     *
     * @return array
     */
    public function dataIdentifierTokenization()
    {
        return [
            [
                '/* testNamespaceDeclaration */',
                [
                    [
                        'type'    => 'T_NAMESPACE',
                        'content' => 'namespace',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Package',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testNamespaceDeclarationWithLevels */',
                [
                    [
                        'type'    => 'T_NAMESPACE',
                        'content' => 'namespace',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Vendor\SubLevel\Domain',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testNamespaceDeclarationWithReservedKeywords */',
                [
                    [
                        'type'    => 'T_NAMESPACE',
                        'content' => 'namespace',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'For\Include\Fn',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testUseStatement */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testUseStatementWithLevels */',
                [
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Vendor\Level\Domain',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testFunctionUseStatement */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_name',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testFunctionUseStatementWithLevels */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Vendor\Level\function_in_ns',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testConstantUseStatement */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'const',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'CONSTANT_NAME',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testConstantUseStatementWithLevels */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'const',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Vendor\Level\OTHER_CONSTANT',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],

            [
                '/* testMultiUseUnqualified */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'UnqualifiedClassName',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                ],
            ],
            [
                '/* testMultiUsePartiallyQualified */',
                [
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Sublevel\PartiallyClassName',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testGroupUseStatement */',
                [
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Vendor\Level',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_OPEN_USE_GROUP',
                        'content' => '{',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'AnotherDomain',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_grouped',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'const',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'CONSTANT_GROUPED',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Sub\YetAnotherDomain',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'SubLevelA\function_grouped_too',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'const',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'SubLevelB\CONSTANT_GROUPED_TOO',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_CLOSE_USE_GROUP',
                        'content' => '}',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testClassName */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'MyClass',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testExtendedFQN */',
                [
                    [
                        'type'    => 'T_NAME_FULLY_QUALIFIED',
                        'content' => '\Vendor\Level\FQN',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testImplementsRelative */',
                [
                    [
                        'type'    => 'T_NAME_RELATIVE',
                        'content' => 'namespace\Name',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                ],
            ],
            [
                '/* testImplementsFQN */',
                [
                    [
                        'type'    => 'T_NAME_FULLY_QUALIFIED',
                        'content' => '\Fully\Qualified',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                ],
            ],
            [
                '/* testImplementsUnqualified */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Unqualified',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                ],
            ],
            [
                '/* testImplementsPartiallyQualifiedWithReservedKeyword */',
                [
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Exit\Level\Name',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testFunctionName */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_name',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            [
                '/* testTypeDeclarationRelative */',
                [
                    [
                        'type'    => 'T_NAME_RELATIVE',
                        'content' => 'namespace\Name',
                    ],
                    [
                        'type'    => 'T_TYPE_UNION',
                        'content' => '|',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'object',
                    ],
                ],
            ],
            [
                '/* testTypeDeclarationFQN */',
                [
                    [
                        'type'    => 'T_NAME_FULLY_QUALIFIED',
                        'content' => '\Fully\Qualified\Name',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            [
                '/* testTypeDeclarationUnqualified */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Unqualified',
                    ],
                    [
                        'type'    => 'T_TYPE_UNION',
                        'content' => '|',
                    ],
                    [
                        'type'    => 'T_FALSE',
                        'content' => 'false',
                    ],
                ],
            ],
            [
                '/* testTypeDeclarationPartiallyQualified */',
                [
                    [
                        'type'    => 'T_NULLABLE',
                        'content' => '?',
                    ],
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Sublevel\Name',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            [
                '/* testReturnTypeFQN */',
                [
                    [
                        'type'    => 'T_NULLABLE',
                        'content' => '?',
                    ],
                    [
                        'type'    => 'T_NAME_FULLY_QUALIFIED',
                        'content' => '\Name',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            [
                '/* testFunctionCallRelative */',
                [
                    [
                        'type'    => 'T_NAME_RELATIVE',
                        'content' => 'NameSpace\function_name',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            [
                '/* testFunctionCallFQNWithReservedKeyword */',
                [
                    [
                        'type'    => 'T_NAME_FULLY_QUALIFIED',
                        'content' => '\Vendor\Foreach\function_name',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            [
                '/* testFunctionCallUnqualified */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_name',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            [
                '/* testFunctionPartiallyQualified */',
                [
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Level\function_name',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            [
                '/* testCatchRelative */',
                [
                    [
                        'type'    => 'T_NAME_RELATIVE',
                        'content' => 'namespace\SubLevel\Exception',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            [
                '/* testCatchFQN */',
                [
                    [
                        'type'    => 'T_NAME_FULLY_QUALIFIED',
                        'content' => '\Exception',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            [
                '/* testCatchUnqualified */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Exception',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            [
                '/* testCatchPartiallyQualified */',
                [
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Level\Exception',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],

            [
                '/* testNewRelative */',
                [
                    [
                        'type'    => 'T_NAME_RELATIVE',
                        'content' => 'namespace\ClassName',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            [
                '/* testNewFQN */',
                [
                    [
                        'type'    => 'T_NAME_FULLY_QUALIFIED',
                        'content' => '\Vendor\ClassName',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            [
                '/* testNewUnqualified */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testNewPartiallyQualified */',
                [
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Level\ClassName',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testDoubleColonRelative */',
                [
                    [
                        'type'    => 'T_NAME_RELATIVE',
                        'content' => 'namespace\ClassName',
                    ],
                    [
                        'type'    => 'T_DOUBLE_COLON',
                        'content' => '::',
                    ],
                ],
            ],
            [
                '/* testDoubleColonFQN */',
                [
                    [
                        'type'    => 'T_NAME_FULLY_QUALIFIED',
                        'content' => '\ClassName',
                    ],
                    [
                        'type'    => 'T_DOUBLE_COLON',
                        'content' => '::',
                    ],
                ],
            ],
            [
                '/* testDoubleColonUnqualified */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_DOUBLE_COLON',
                        'content' => '::',
                    ],
                ],
            ],
            [
                '/* testDoubleColonPartiallyQualified */',
                [
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Level\ClassName',
                    ],
                    [
                        'type'    => 'T_DOUBLE_COLON',
                        'content' => '::',
                    ],
                ],
            ],
            [
                '/* testInstanceOfRelative */',
                [
                    [
                        'type'    => 'T_NAME_RELATIVE',
                        'content' => 'namespace\ClassName',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testInstanceOfFQN */',
                [
                    [
                        'type'    => 'T_NAME_FULLY_QUALIFIED',
                        'content' => '\Full\ClassName',
                    ],
                    [
                        'type'    => 'T_CLOSE_PARENTHESIS',
                        'content' => ')',
                    ],
                ],
            ],
            [
                '/* testInstanceOfUnqualified */',
                [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            [
                '/* testInstanceOfPartiallyQualified */',
                [
                    [
                        'type'    => 'T_NAME_QUALIFIED',
                        'content' => 'Partially\ClassName',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            [
                '/* testInvalidInPHP8Whitespace */',
                [
                    [
                        'type'    => 'T_NAMESPACE',
                        'content' => 'namespace',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Sublevel',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '          ',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_name',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            [
                '/* testInvalidInPHP8Comments */',
                [
                    [
                        'type'    => 'T_NAME_FULLY_QUALIFIED',
                        'content' => '\Fully',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => '// phpcs:ignore Stnd.Cat.Sniff -- for reasons
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_NAME_FULLY_QUALIFIED',
                        'content' => '\Qualified',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '/* comment */',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_NAME_FULLY_QUALIFIED',
                        'content' => '\Name',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testInvalidDoubleBackslash */',
                [
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_NAME_FULLY_QUALIFIED',
                        'content' => '\SomeClass',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
        ];

    }//end dataIdentifierTokenization()


}//end class
