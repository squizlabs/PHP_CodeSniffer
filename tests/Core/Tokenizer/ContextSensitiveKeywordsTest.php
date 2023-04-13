<?php
/**
 * Tests the conversion of context sensitive keywords to T_STRING.
 *
 * @author    Jaroslav Hanslík <kukulich@kukulich.cz>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Tokens;

class ContextSensitiveKeywordsTest extends AbstractMethodUnitTest
{


    /**
     * Test that context sensitive keyword is tokenized as string when it should be string.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataStrings
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testStrings($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken($testMarker, (Tokens::$contextSensitiveKeywords + [T_STRING, T_NULL, T_FALSE, T_TRUE, T_PARENT, T_SELF]));

        $this->assertSame(T_STRING, $tokens[$token]['code']);
        $this->assertSame('T_STRING', $tokens[$token]['type']);

    }//end testStrings()


    /**
     * Data provider.
     *
     * @see testStrings()
     *
     * @return array
     */
    public function dataStrings()
    {
        return [
            ['/* testAbstract */'],
            ['/* testArray */'],
            ['/* testAs */'],
            ['/* testBreak */'],
            ['/* testCallable */'],
            ['/* testCase */'],
            ['/* testCatch */'],
            ['/* testClass */'],
            ['/* testClone */'],
            ['/* testConst */'],
            ['/* testContinue */'],
            ['/* testDeclare */'],
            ['/* testDefault */'],
            ['/* testDo */'],
            ['/* testEcho */'],
            ['/* testElse */'],
            ['/* testElseIf */'],
            ['/* testEmpty */'],
            ['/* testEndDeclare */'],
            ['/* testEndFor */'],
            ['/* testEndForeach */'],
            ['/* testEndIf */'],
            ['/* testEndSwitch */'],
            ['/* testEndWhile */'],
            ['/* testEnum */'],
            ['/* testEval */'],
            ['/* testExit */'],
            ['/* testExtends */'],
            ['/* testFinal */'],
            ['/* testFinally */'],
            ['/* testFn */'],
            ['/* testFor */'],
            ['/* testForeach */'],
            ['/* testFunction */'],
            ['/* testGlobal */'],
            ['/* testGoto */'],
            ['/* testIf */'],
            ['/* testImplements */'],
            ['/* testInclude */'],
            ['/* testIncludeOnce */'],
            ['/* testInstanceOf */'],
            ['/* testInsteadOf */'],
            ['/* testInterface */'],
            ['/* testIsset */'],
            ['/* testList */'],
            ['/* testMatch */'],
            ['/* testNamespace */'],
            ['/* testNew */'],
            ['/* testParent */'],
            ['/* testPrint */'],
            ['/* testPrivate */'],
            ['/* testProtected */'],
            ['/* testPublic */'],
            ['/* testReadonly */'],
            ['/* testRequire */'],
            ['/* testRequireOnce */'],
            ['/* testReturn */'],
            ['/* testSelf */'],
            ['/* testStatic */'],
            ['/* testSwitch */'],
            ['/* testThrows */'],
            ['/* testTrait */'],
            ['/* testTry */'],
            ['/* testUnset */'],
            ['/* testUse */'],
            ['/* testVar */'],
            ['/* testWhile */'],
            ['/* testYield */'],
            ['/* testYieldFrom */'],
            ['/* testAnd */'],
            ['/* testOr */'],
            ['/* testXor */'],
            ['/* testFalse */'],
            ['/* testTrue */'],
            ['/* testNull */'],

            ['/* testKeywordAfterNamespaceShouldBeString */'],
            ['/* testNamespaceNameIsString1 */'],
            ['/* testNamespaceNameIsString2 */'],
            ['/* testNamespaceNameIsString3 */'],

            ['/* testKeywordAfterFunctionShouldBeString */'],
            ['/* testKeywordAfterFunctionByRefShouldBeString */'],
            ['/* testKeywordSelfAfterFunctionByRefShouldBeString */'],
            ['/* testKeywordStaticAfterFunctionByRefShouldBeString */'],
            ['/* testKeywordParentAfterFunctionByRefShouldBeString */'],
            ['/* testKeywordFalseAfterFunctionByRefShouldBeString */'],
            ['/* testKeywordTrueAfterFunctionByRefShouldBeString */'],
            ['/* testKeywordNullAfterFunctionByRefShouldBeString */'],

            ['/* testKeywordAsFunctionCallNameShouldBeStringSelf */'],
            ['/* testKeywordAsFunctionCallNameShouldBeStringStatic */'],
            ['/* testKeywordAsMethodCallNameShouldBeStringStatic */'],
            ['/* testKeywordAsFunctionCallNameShouldBeStringParent */'],
            ['/* testKeywordAsFunctionCallNameShouldBeStringFalse */'],
            ['/* testKeywordAsFunctionCallNameShouldBeStringTrue */'],
            ['/* testKeywordAsFunctionCallNameShouldBeStringNull */'],

            ['/* testClassInstantiationFalseIsString */'],
            ['/* testClassInstantiationTrueIsString */'],
            ['/* testClassInstantiationNullIsString */'],
        ];

    }//end dataStrings()


    /**
     * Test that context sensitive keyword is tokenized as keyword when it should be keyword.
     *
     * @param string $testMarker        The comment which prefaces the target token in the test file.
     * @param string $expectedTokenType The expected token type.
     *
     * @dataProvider dataKeywords
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testKeywords($testMarker, $expectedTokenType)
    {
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken(
            $testMarker,
            (Tokens::$contextSensitiveKeywords + [T_ANON_CLASS, T_MATCH_DEFAULT, T_PARENT, T_SELF, T_STRING, T_NULL, T_FALSE, T_TRUE])
        );

        $this->assertSame(constant($expectedTokenType), $tokens[$token]['code']);
        $this->assertSame($expectedTokenType, $tokens[$token]['type']);

    }//end testKeywords()


    /**
     * Data provider.
     *
     * @see testKeywords()
     *
     * @return array
     */
    public function dataKeywords()
    {
        return [
            [
                '/* testNamespaceIsKeyword */',
                'T_NAMESPACE',
            ],
            [
                '/* testAbstractIsKeyword */',
                'T_ABSTRACT',
            ],
            [
                '/* testClassIsKeyword */',
                'T_CLASS',
            ],
            [
                '/* testExtendsIsKeyword */',
                'T_EXTENDS',
            ],
            [
                '/* testImplementsIsKeyword */',
                'T_IMPLEMENTS',
            ],
            [
                '/* testUseIsKeyword */',
                'T_USE',
            ],
            [
                '/* testInsteadOfIsKeyword */',
                'T_INSTEADOF',
            ],
            [
                '/* testAsIsKeyword */',
                'T_AS',
            ],
            [
                '/* testConstIsKeyword */',
                'T_CONST',
            ],
            [
                '/* testPrivateIsKeyword */',
                'T_PRIVATE',
            ],
            [
                '/* testProtectedIsKeyword */',
                'T_PROTECTED',
            ],
            [
                '/* testPublicIsKeyword */',
                'T_PUBLIC',
            ],
            [
                '/* testVarIsKeyword */',
                'T_VAR',
            ],
            [
                '/* testStaticIsKeyword */',
                'T_STATIC',
            ],
            [
                '/* testReadonlyIsKeyword */',
                'T_READONLY',
            ],
            [
                '/* testFinalIsKeyword */',
                'T_FINAL',
            ],
            [
                '/* testFunctionIsKeyword */',
                'T_FUNCTION',
            ],
            [
                '/* testCallableIsKeyword */',
                'T_CALLABLE',
            ],
            [
                '/* testSelfIsKeyword */',
                'T_SELF',
            ],
            [
                '/* testParentIsKeyword */',
                'T_PARENT',
            ],
            [
                '/* testReturnIsKeyword */',
                'T_RETURN',
            ],

            [
                '/* testInterfaceIsKeyword */',
                'T_INTERFACE',
            ],
            [
                '/* testTraitIsKeyword */',
                'T_TRAIT',
            ],
            [
                '/* testEnumIsKeyword */',
                'T_ENUM',
            ],

            [
                '/* testNewIsKeyword */',
                'T_NEW',
            ],
            [
                '/* testInstanceOfIsKeyword */',
                'T_INSTANCEOF',
            ],
            [
                '/* testCloneIsKeyword */',
                'T_CLONE',
            ],

            [
                '/* testIfIsKeyword */',
                'T_IF',
            ],
            [
                '/* testEmptyIsKeyword */',
                'T_EMPTY',
            ],
            [
                '/* testElseIfIsKeyword */',
                'T_ELSEIF',
            ],
            [
                '/* testElseIsKeyword */',
                'T_ELSE',
            ],
            [
                '/* testEndIfIsKeyword */',
                'T_ENDIF',
            ],

            [
                '/* testForIsKeyword */',
                'T_FOR',
            ],
            [
                '/* testEndForIsKeyword */',
                'T_ENDFOR',
            ],

            [
                '/* testForeachIsKeyword */',
                'T_FOREACH',
            ],
            [
                '/* testEndForeachIsKeyword */',
                'T_ENDFOREACH',
            ],

            [
                '/* testSwitchIsKeyword */',
                'T_SWITCH',
            ],
            [
                '/* testCaseIsKeyword */',
                'T_CASE',
            ],
            [
                '/* testDefaultIsKeyword */',
                'T_DEFAULT',
            ],
            [
                '/* testEndSwitchIsKeyword */',
                'T_ENDSWITCH',
            ],
            [
                '/* testBreakIsKeyword */',
                'T_BREAK',
            ],
            [
                '/* testContinueIsKeyword */',
                'T_CONTINUE',
            ],

            [
                '/* testDoIsKeyword */',
                'T_DO',
            ],
            [
                '/* testWhileIsKeyword */',
                'T_WHILE',
            ],
            [
                '/* testEndWhileIsKeyword */',
                'T_ENDWHILE',
            ],

            [
                '/* testTryIsKeyword */',
                'T_TRY',
            ],
            [
                '/* testThrowIsKeyword */',
                'T_THROW',
            ],
            [
                '/* testCatchIsKeyword */',
                'T_CATCH',
            ],
            [
                '/* testFinallyIsKeyword */',
                'T_FINALLY',
            ],

            [
                '/* testGlobalIsKeyword */',
                'T_GLOBAL',
            ],
            [
                '/* testEchoIsKeyword */',
                'T_ECHO',
            ],
            [
                '/* testPrintIsKeyword */',
                'T_PRINT',
            ],
            [
                '/* testDieIsKeyword */',
                'T_EXIT',
            ],
            [
                '/* testEvalIsKeyword */',
                'T_EVAL',
            ],
            [
                '/* testExitIsKeyword */',
                'T_EXIT',
            ],
            [
                '/* testIssetIsKeyword */',
                'T_ISSET',
            ],
            [
                '/* testUnsetIsKeyword */',
                'T_UNSET',
            ],

            [
                '/* testIncludeIsKeyword */',
                'T_INCLUDE',
            ],
            [
                '/* testIncludeOnceIsKeyword */',
                'T_INCLUDE_ONCE',
            ],
            [
                '/* testRequireIsKeyword */',
                'T_REQUIRE',
            ],
            [
                '/* testRequireOnceIsKeyword */',
                'T_REQUIRE_ONCE',
            ],

            [
                '/* testListIsKeyword */',
                'T_LIST',
            ],
            [
                '/* testGotoIsKeyword */',
                'T_GOTO',
            ],
            [
                '/* testMatchIsKeyword */',
                'T_MATCH',
            ],
            [
                '/* testMatchDefaultIsKeyword */',
                'T_MATCH_DEFAULT',
            ],
            [
                '/* testFnIsKeyword */',
                'T_FN',
            ],

            [
                '/* testYieldIsKeyword */',
                'T_YIELD',
            ],
            [
                '/* testYieldFromIsKeyword */',
                'T_YIELD_FROM',
            ],

            [
                '/* testDeclareIsKeyword */',
                'T_DECLARE',
            ],
            [
                '/* testEndDeclareIsKeyword */',
                'T_ENDDECLARE',
            ],

            [
                '/* testAndIsKeyword */',
                'T_LOGICAL_AND',
            ],
            [
                '/* testOrIsKeyword */',
                'T_LOGICAL_OR',
            ],
            [
                '/* testXorIsKeyword */',
                'T_LOGICAL_XOR',
            ],

            [
                '/* testAnonymousClassIsKeyword */',
                'T_ANON_CLASS',
            ],
            [
                '/* testExtendsInAnonymousClassIsKeyword */',
                'T_EXTENDS',
            ],
            [
                '/* testImplementsInAnonymousClassIsKeyword */',
                'T_IMPLEMENTS',
            ],
            [
                '/* testClassInstantiationParentIsKeyword */',
                'T_PARENT',
            ],
            [
                '/* testClassInstantiationSelfIsKeyword */',
                'T_SELF',
            ],
            [
                '/* testClassInstantiationStaticIsKeyword */',
                'T_STATIC',
            ],
            [
                '/* testNamespaceInNameIsKeyword */',
                'T_NAMESPACE',
            ],

            [
                '/* testStaticIsKeywordBeforeClosure */',
                'T_STATIC',
            ],
            [
                '/* testStaticIsKeywordWhenParamType */',
                'T_STATIC',
            ],
            [
                '/* testStaticIsKeywordBeforeArrow */',
                'T_STATIC',
            ],
            [
                '/* testStaticIsKeywordWhenReturnType */',
                'T_STATIC',
            ],

            [
                '/* testFalseIsKeywordAsParamType */',
                'T_FALSE',
            ],
            [
                '/* testTrueIsKeywordAsParamType */',
                'T_TRUE',
            ],
            [
                '/* testNullIsKeywordAsParamType */',
                'T_NULL',
            ],
            [
                '/* testFalseIsKeywordAsReturnType */',
                'T_FALSE',
            ],
            [
                '/* testTrueIsKeywordAsReturnType */',
                'T_TRUE',
            ],
            [
                '/* testNullIsKeywordAsReturnType */',
                'T_NULL',
            ],
            [
                '/* testFalseIsKeywordInComparison */',
                'T_FALSE',
            ],
            [
                '/* testTrueIsKeywordInComparison */',
                'T_TRUE',
            ],
            [
                '/* testNullIsKeywordInComparison */',
                'T_NULL',
            ],
        ];

    }//end dataKeywords()


}//end class
