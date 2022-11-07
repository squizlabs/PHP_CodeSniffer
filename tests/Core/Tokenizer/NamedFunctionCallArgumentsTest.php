<?php
/**
 * Tests the backfilling of the T_FN token to PHP < 7.4.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Tokens;

class NamedFunctionCallArgumentsTest extends AbstractMethodUnitTest
{


    /**
     * Verify that parameter labels are tokenized as T_PARAM_NAME and that
     * the colon after it is tokenized as a T_COLON.
     *
     * @param string $testMarker The comment prefacing the target token.
     * @param array  $parameters The token content for each parameter label to look for.
     *
     * @dataProvider dataNamedFunctionCallArguments
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testNamedFunctionCallArguments($testMarker, $parameters)
    {
        $tokens = self::$phpcsFile->getTokens();

        foreach ($parameters as $content) {
            $label = $this->getTargetToken($testMarker, [T_STRING, T_PARAM_NAME], $content);

            $this->assertSame(
                T_PARAM_NAME,
                $tokens[$label]['code'],
                'Token tokenized as '.$tokens[$label]['type'].', not T_PARAM_NAME (code)'
            );
            $this->assertSame(
                'T_PARAM_NAME',
                $tokens[$label]['type'],
                'Token tokenized as '.$tokens[$label]['type'].', not T_PARAM_NAME (type)'
            );

            // Get the next non-empty token.
            $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

            $this->assertSame(
                ':',
                $tokens[$colon]['content'],
                'Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
            );
            $this->assertSame(
                T_COLON,
                $tokens[$colon]['code'],
                'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
            );
            $this->assertSame(
                'T_COLON',
                $tokens[$colon]['type'],
                'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
            );
        }//end foreach

    }//end testNamedFunctionCallArguments()


    /**
     * Data provider.
     *
     * @see testNamedFunctionCallArguments()
     *
     * @return array
     */
    public function dataNamedFunctionCallArguments()
    {
        return [
            [
                '/* testNamedArgs */',
                [
                    'start_index',
                    'count',
                    'value',
                ],
            ],
            [
                '/* testNamedArgsMultiline */',
                [
                    'start_index',
                    'count',
                    'value',
                ],
            ],
            [
                '/* testNamedArgsWithWhitespaceAndComments */',
                [
                    'start_index',
                    'count',
                    'value',
                ],
            ],
            [
                '/* testMixedPositionalAndNamedArgs */',
                ['double_encode'],
            ],
            [
                '/* testNestedFunctionCallOuter */',
                [
                    'start_index',
                    'count',
                    'value',
                ],
            ],
            [
                '/* testNestedFunctionCallInner1 */',
                ['skip'],
            ],
            [
                '/* testNestedFunctionCallInner2 */',
                ['array_or_countable'],
            ],
            [
                '/* testNamespaceOperatorFunction */',
                [
                    'label',
                    'more',
                ],
            ],
            [
                '/* testNamespaceRelativeFunction */',
                [
                    'label',
                    'more',
                ],
            ],
            [
                '/* testNamespacedFQNFunction */',
                [
                    'label',
                    'more',
                ],
            ],
            [
                '/* testVariableFunction */',
                [
                    'label',
                    'more',
                ],
            ],
            [
                '/* testVariableVariableFunction */',
                [
                    'label',
                    'more',
                ],
            ],
            [
                '/* testMethodCall */',
                [
                    'label',
                    'more',
                ],
            ],
            [
                '/* testVariableMethodCall */',
                [
                    'label',
                    'more',
                ],
            ],
            [
                '/* testClassInstantiation */',
                [
                    'label',
                    'more',
                ],
            ],
            [
                '/* testClassInstantiationSelf */',
                [
                    'label',
                    'more',
                ],
            ],
            [
                '/* testClassInstantiationStatic */',
                [
                    'label',
                    'more',
                ],
            ],
            [
                '/* testAnonClass */',
                [
                    'label',
                    'more',
                ],
            ],
            [
                '/* testNonAsciiNames */',
                [
                    '💩💩💩',
                    'Пасха',
                    '_valid',
                ],
            ],

            // Coding errors which should still be handled.
            [
                '/* testCompileErrorNamedBeforePositional */',
                ['param'],
            ],
            [
                '/* testDuplicateName1 */',
                ['param'],
            ],
            [
                '/* testDuplicateName2 */',
                ['param'],
            ],
            [
                '/* testIncorrectOrderWithVariadic */',
                ['start_index'],
            ],
            [
                '/* testCompileErrorIncorrectOrderWithVariadic */',
                ['param'],
            ],
            [
                '/* testParseErrorNoValue */',
                [
                    'param1',
                    'param2',
                ],
            ],
            [
                '/* testParseErrorExit */',
                ['status'],
            ],
            [
                '/* testParseErrorEmpty */',
                ['variable'],
            ],
            [
                '/* testParseErrorEval */',
                ['code'],
            ],
            [
                '/* testParseErrorArbitraryParentheses */',
                ['something'],
            ],
        ];

    }//end dataNamedFunctionCallArguments()


    /**
     * Verify that other T_STRING tokens within a function call are still tokenized as T_STRING.
     *
     * @param string $testMarker The comment prefacing the target token.
     * @param string $content    The token content to look for.
     *
     * @dataProvider dataOtherTstringInFunctionCall
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testOtherTstringInFunctionCall($testMarker, $content)
    {
        $tokens = self::$phpcsFile->getTokens();

        $label = $this->getTargetToken($testMarker, [T_STRING, T_PARAM_NAME], $content);

        $this->assertSame(
            T_STRING,
            $tokens[$label]['code'],
            'Token tokenized as '.$tokens[$label]['type'].', not T_STRING (code)'
        );
        $this->assertSame(
            'T_STRING',
            $tokens[$label]['type'],
            'Token tokenized as '.$tokens[$label]['type'].', not T_STRING (type)'
        );

    }//end testOtherTstringInFunctionCall()


    /**
     * Data provider.
     *
     * @see testOtherTstringInFunctionCall()
     *
     * @return array
     */
    public function dataOtherTstringInFunctionCall()
    {
        return [
            [
                '/* testPositionalArgs */',
                'START_INDEX',
            ],
            [
                '/* testPositionalArgs */',
                'COUNT',
            ],
            [
                '/* testPositionalArgs */',
                'VALUE',
            ],
            [
                '/* testNestedFunctionCallInner2 */',
                'count',
            ],
        ];

    }//end dataOtherTstringInFunctionCall()


    /**
     * Verify whether the colons are tokenized correctly when a ternary is used in a mixed
     * positional and named arguments function call.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testMixedPositionalAndNamedArgsWithTernary()
    {
        $tokens = self::$phpcsFile->getTokens();

        $true = $this->getTargetToken('/* testMixedPositionalAndNamedArgsWithTernary */', T_TRUE);

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($true + 1), null, true);

        $this->assertSame(
            T_INLINE_ELSE,
            $tokens[$colon]['code'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (code)'
        );
        $this->assertSame(
            'T_INLINE_ELSE',
            $tokens[$colon]['type'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (type)'
        );

        $label = $this->getTargetToken('/* testMixedPositionalAndNamedArgsWithTernary */', T_PARAM_NAME, 'name');

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

    }//end testMixedPositionalAndNamedArgsWithTernary()


    /**
     * Verify whether the colons are tokenized correctly when a ternary is used
     * in a named arguments function call.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testNamedArgWithTernary()
    {
        $tokens = self::$phpcsFile->getTokens();

        /*
         * First argument.
         */

        $label = $this->getTargetToken('/* testNamedArgWithTernary */', T_PARAM_NAME, 'label');

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'First arg: Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'First arg: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'First arg: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

        $true = $this->getTargetToken('/* testNamedArgWithTernary */', T_TRUE);

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($true + 1), null, true);

        $this->assertSame(
            T_INLINE_ELSE,
            $tokens[$colon]['code'],
            'First arg ternary: Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (code)'
        );
        $this->assertSame(
            'T_INLINE_ELSE',
            $tokens[$colon]['type'],
            'First arg ternary: Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (type)'
        );

        /*
         * Second argument.
         */

        $label = $this->getTargetToken('/* testNamedArgWithTernary */', T_PARAM_NAME, 'more');

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'Second arg: Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Second arg: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Second arg: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

        $true = $this->getTargetToken('/* testNamedArgWithTernary */', T_STRING, 'CONSTANT_A');

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($true + 1), null, true);

        $this->assertSame(
            T_INLINE_ELSE,
            $tokens[$colon]['code'],
            'Second arg ternary: Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (code)'
        );
        $this->assertSame(
            'T_INLINE_ELSE',
            $tokens[$colon]['type'],
            'Second arg ternary: Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (type)'
        );

    }//end testNamedArgWithTernary()


    /**
     * Verify whether the colons are tokenized correctly when named arguments
     * function calls are used in a ternary.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testTernaryWithFunctionCallsInThenElse()
    {
        $tokens = self::$phpcsFile->getTokens();

        /*
         * Then.
         */

        $label = $this->getTargetToken('/* testTernaryWithFunctionCallsInThenElse */', T_PARAM_NAME, 'label');

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'Function in then: Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Function in then: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Function in then: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

        $closeParens = $this->getTargetToken('/* testTernaryWithFunctionCallsInThenElse */', T_CLOSE_PARENTHESIS);

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($closeParens + 1), null, true);

        $this->assertSame(
            T_INLINE_ELSE,
            $tokens[$colon]['code'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (code)'
        );
        $this->assertSame(
            'T_INLINE_ELSE',
            $tokens[$colon]['type'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (type)'
        );

        /*
         * Else.
         */

        $label = $this->getTargetToken('/* testTernaryWithFunctionCallsInThenElse */', T_PARAM_NAME, 'more');

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'Function in else: Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Function in else: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Function in else: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

    }//end testTernaryWithFunctionCallsInThenElse()


    /**
     * Verify whether the colons are tokenized correctly when constants are used in a ternary.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testTernaryWithConstantsInThenElse()
    {
        $tokens = self::$phpcsFile->getTokens();

        $constant = $this->getTargetToken('/* testTernaryWithConstantsInThenElse */', T_STRING, 'CONSTANT_NAME');

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($constant + 1), null, true);

        $this->assertSame(
            T_INLINE_ELSE,
            $tokens[$colon]['code'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (code)'
        );
        $this->assertSame(
            'T_INLINE_ELSE',
            $tokens[$colon]['type'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_INLINE_ELSE (type)'
        );

    }//end testTernaryWithConstantsInThenElse()


    /**
     * Verify whether the colons are tokenized correctly in a switch statement.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testSwitchStatement()
    {
        $tokens = self::$phpcsFile->getTokens();

        $label = $this->getTargetToken('/* testSwitchCaseWithConstant */', T_STRING, 'MY_CONSTANT');

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'First case: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'First case: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

        $label = $this->getTargetToken('/* testSwitchCaseWithClassProperty */', T_STRING, 'property');

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Second case: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Second case: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

        $default = $this->getTargetToken('/* testSwitchDefault */', T_DEFAULT);

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($default + 1), null, true);

        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Default case: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Default case: Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

    }//end testSwitchStatement()


    /**
     * Verify that a variable parameter label (parse error) is still tokenized as T_VARIABLE.
     *
     * @covers PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testParseErrorVariableLabel()
    {
        $tokens = self::$phpcsFile->getTokens();

        $label = $this->getTargetToken('/* testParseErrorDynamicName */', [T_VARIABLE, T_PARAM_NAME], '$variableStoringParamName');

        $this->assertSame(
            T_VARIABLE,
            $tokens[$label]['code'],
            'Token tokenized as '.$tokens[$label]['type'].', not T_VARIABLE (code)'
        );
        $this->assertSame(
            'T_VARIABLE',
            $tokens[$label]['type'],
            'Token tokenized as '.$tokens[$label]['type'].', not T_VARIABLE (type)'
        );

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

    }//end testParseErrorVariableLabel()


    /**
     * Verify that reserved keywords used as a parameter label are tokenized as T_PARAM_NAME
     * and that the colon after it is tokenized as a T_COLON.
     *
     * @param string $testMarker   The comment prefacing the target token.
     * @param array  $tokenTypes   The token codes to look for.
     * @param string $tokenContent The token content to look for.
     *
     * @dataProvider dataReservedKeywordsAsName
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testReservedKeywordsAsName($testMarker, $tokenTypes, $tokenContent)
    {
        $tokens = self::$phpcsFile->getTokens();
        $label  = $this->getTargetToken($testMarker, $tokenTypes, $tokenContent);

        $this->assertSame(
            T_PARAM_NAME,
            $tokens[$label]['code'],
            'Token tokenized as '.$tokens[$label]['type'].', not T_PARAM_NAME (code)'
        );
        $this->assertSame(
            'T_PARAM_NAME',
            $tokens[$label]['type'],
            'Token tokenized as '.$tokens[$label]['type'].', not T_PARAM_NAME (type)'
        );

        // Get the next non-empty token.
        $colon = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($label + 1), null, true);

        $this->assertSame(
            ':',
            $tokens[$colon]['content'],
            'Next token after parameter name is not a colon. Found: '.$tokens[$colon]['content']
        );
        $this->assertSame(
            T_COLON,
            $tokens[$colon]['code'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (code)'
        );
        $this->assertSame(
            'T_COLON',
            $tokens[$colon]['type'],
            'Token tokenized as '.$tokens[$colon]['type'].', not T_COLON (type)'
        );

    }//end testReservedKeywordsAsName()


    /**
     * Data provider.
     *
     * @see testReservedKeywordsAsName()
     *
     * @return array
     */
    public function dataReservedKeywordsAsName()
    {
        $reservedKeywords = [
            // '__halt_compiler', NOT TESTABLE
            'abstract',
            'and',
            'array',
            'as',
            'break',
            'callable',
            'case',
            'catch',
            'class',
            'clone',
            'const',
            'continue',
            'declare',
            'default',
            'die',
            'do',
            'echo',
            'else',
            'elseif',
            'empty',
            'enddeclare',
            'endfor',
            'endforeach',
            'endif',
            'endswitch',
            'endwhile',
            'enum',
            'eval',
            'exit',
            'extends',
            'final',
            'finally',
            'fn',
            'for',
            'foreach',
            'function',
            'global',
            'goto',
            'if',
            'implements',
            'include',
            'include_once',
            'instanceof',
            'insteadof',
            'interface',
            'isset',
            'list',
            'match',
            'namespace',
            'new',
            'or',
            'print',
            'private',
            'protected',
            'public',
            'readonly',
            'require',
            'require_once',
            'return',
            'static',
            'switch',
            'throw',
            'trait',
            'try',
            'unset',
            'use',
            'var',
            'while',
            'xor',
            'yield',
            'int',
            'float',
            'bool',
            'string',
            'true',
            'false',
            'null',
            'void',
            'iterable',
            'object',
            'resource',
            'mixed',
            'numeric',
            'never',

            // Not reserved keyword, but do have their own token in PHPCS.
            'parent',
            'self',
        ];

        $data = [];

        foreach ($reservedKeywords as $keyword) {
            $tokensTypes = [
                T_PARAM_NAME,
                T_STRING,
                T_GOTO_LABEL,
            ];
            $tokenName   = 'T_'.strtoupper($keyword);

            if ($keyword === 'and') {
                $tokensTypes[] = T_LOGICAL_AND;
            } else if ($keyword === 'die') {
                $tokensTypes[] = T_EXIT;
            } else if ($keyword === 'or') {
                $tokensTypes[] = T_LOGICAL_OR;
            } else if ($keyword === 'xor') {
                $tokensTypes[] = T_LOGICAL_XOR;
            } else if ($keyword === '__halt_compiler') {
                $tokensTypes[] = T_HALT_COMPILER;
            } else if (defined($tokenName) === true) {
                $tokensTypes[] = constant($tokenName);
            }

            $data[$keyword.'FirstParam'] = [
                '/* testReservedKeyword'.ucfirst($keyword).'1 */',
                $tokensTypes,
                $keyword,
            ];

            $data[$keyword.'SecondParam'] = [
                '/* testReservedKeyword'.ucfirst($keyword).'2 */',
                $tokensTypes,
                $keyword,
            ];
        }//end foreach

        return $data;

    }//end dataReservedKeywordsAsName()


}//end class
