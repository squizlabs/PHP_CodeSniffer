<?php
/**
 * Tests for various methods in the \PHP_CodeSniffer\Util\Sniffs\Conditions class.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Conditions;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Conditions;
use PHP_CodeSniffer\Util\Tokens;

class GetConditionTest extends AbstractMethodUnitTest
{

    /**
     * List of all the test markers with their target token in the test case file.
     *
     * - The startPoint token is left out as it is tested separately.
     * - The key is the type of token to look for after the test marker.
     *
     * @var array <int|string> => <string>
     */
    public static $testTargets = [
        T_VARIABLE                 => '/* testSeriouslyNestedMethod */',
        T_RETURN                   => '/* testDeepestNested */',
        T_ECHO                     => '/* testInException */',
        T_CONSTANT_ENCAPSED_STRING => '/* testInDefault */',
    ];

    /**
     * Cache for the test token stack pointers.
     *
     * @var array <string> => <int>
     */
    private $testTokens = [];

    /**
     * List of all the condition markers in the test case file.
     *
     * @var string[]
     */
    private $conditionMarkers = [
        '/* condition 0: namespace */',
        '/* condition 1: if */',
        '/* condition 2: function */',
        '/* condition 3-1: if */',
        '/* condition 3-2: else */',
        '/* condition 4: if */',
        '/* condition 5: nested class */',
        '/* condition 6: class method */',
        '/* condition 7: switch */',
        '/* condition 8a: case */',
        '/* condition 9: while */',
        '/* condition 10-1: if */',
        '/* condition 11-1: nested anonymous class */',
        '/* condition 12: nested anonymous class method */',
        '/* condition 13: closure */',
        '/* condition 10-2: elseif */',
        '/* condition 10-3: foreach */',
        '/* condition 11-2: try */',
        '/* condition 11-3: catch */',
        '/* condition 11-4: finally */',
        '/* condition 8b: default */',
    ];

    /**
     * Cache for the marker token stack pointers.
     *
     * @var array <string> => <int>
     */
    private $markerTokens = [];

    /**
     * Base array with all the scope opening tokens.
     *
     * This array is merged with expected result arrays for various unit tests
     * to make sure all possible conditions are tested.
     *
     * This array should be kept in sync with the Tokens::$scopeOpeners array.
     * This array isn't auto-generated based on the array in Tokens as for these
     * tests we want to have access to the token constant names, not just their values.
     *
     * @var array <string> => <bool>
     */
    private $conditionDefaults = [
        'T_CLASS'      => false,
        'T_ANON_CLASS' => false,
        'T_INTERFACE'  => false,
        'T_TRAIT'      => false,
        'T_NAMESPACE'  => false,
        'T_FUNCTION'   => false,
        'T_CLOSURE'    => false,
        'T_IF'         => false,
        'T_SWITCH'     => false,
        'T_CASE'       => false,
        'T_DECLARE'    => false,
        'T_DEFAULT'    => false,
        'T_WHILE'      => false,
        'T_ELSE'       => false,
        'T_ELSEIF'     => false,
        'T_FOR'        => false,
        'T_FOREACH'    => false,
        'T_DO'         => false,
        'T_TRY'        => false,
        'T_CATCH'      => false,
        'T_FINALLY'    => false,
        'T_PROPERTY'   => false,
        'T_OBJECT'     => false,
        'T_USE'        => false,
    ];


    /**
     * Set up the token position caches for the tests.
     *
     * Retrieves the test tokens and marker token stack pointer positions
     * only once and caches them as they won't change between the tests anyway.
     *
     * @return void
     */
    protected function setUp()
    {
        if (empty($this->testTokens) === true) {
            foreach (self::$testTargets as $targetToken => $marker) {
                $this->testTokens[$marker] = $this->getTargetToken($marker, $targetToken);
            }
        }

        if (empty($this->markerTokens) === true) {
            foreach ($this->conditionMarkers as $marker) {
                $this->markerTokens[$marker] = $this->getTargetToken($marker, Tokens::$scopeOpeners);
            }
        }

    }//end setUp()


    /**
     * Test passing a non-existent token pointer.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::getCondition
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::hasCondition
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = Conditions::getCondition(self::$phpcsFile, 100000, Tokens::$ooScopeTokens);
        $this->assertFalse($result);

        $result = Conditions::hasCondition(self::$phpcsFile, 100000, T_IF);
        $this->assertFalse($result);

    }//end testNonExistentToken()


    /**
     * Test passing a non conditional token.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::getCondition
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::hasCondition
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::getFirstCondition
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::getLastCondition
     *
     * @return void
     */
    public function testNonConditionalToken()
    {
        $stackPtr = $this->getTargetToken('/* testStartPoint */', T_STRING);

        $result = Conditions::getCondition(self::$phpcsFile, $stackPtr, T_IF);
        $this->assertFalse($result);

        $result = Conditions::hasCondition(self::$phpcsFile, $stackPtr, Tokens::$ooScopeTokens);
        $this->assertFalse($result);

        $result = Conditions::getFirstCondition(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);

        $result = Conditions::getLastCondition(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);

    }//end testNonConditionalToken()


    /**
     * Test retrieving a specific condition from a tokens "conditions" array.
     *
     * @param string $testMarker              The comment which prefaces the target token in the test file.
     * @param array  $expectedResults         Array with the condition token type to search for as key
     *                                        and the marker for the expected stack pointer result as a value.
     * @param array  $expectedResultsReversed Array with the condition token type to search for as key
     *                                        and the marker for the expected stack pointer result as a value.
     *
     * @dataProvider dataGetCondition
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Conditions::getCondition
     *
     * @return void
     */
    public function testGetCondition($testMarker, $expectedResults, $expectedResultsReversed)
    {
        $stackPtr = $this->testTokens[$testMarker];

        // Add expected results for all test markers not listed in the data provider.
        $expectedResults += $this->conditionDefaults;

        foreach ($expectedResults as $conditionType => $expected) {
            if ($expected !== false) {
                $expected = $this->markerTokens[$expected];
            }

            $result = Conditions::getCondition(self::$phpcsFile, $stackPtr, constant($conditionType));
            $this->assertSame(
                $expected,
                $result,
                "Assertion failed for test marker '{$testMarker}' with condition {$conditionType}"
            );
        }

        foreach ($expectedResultsReversed as $conditionType => $expected) {
            if ($expected !== false) {
                $expected = $this->markerTokens[$expected];
            }

            $result = Conditions::getCondition(self::$phpcsFile, $stackPtr, constant($conditionType), true);
            $this->assertSame(
                $expected,
                $result,
                "Assertion failed for test marker '{$testMarker}' with condition {$conditionType} (reversed)"
            );
        }

    }//end testGetCondition()


    /**
     * Data provider.
     *
     * Only the conditions which are expected to be *found* need to be listed here.
     * All other potential conditions will automatically also be tested and will expect
     * `false` as a result.
     *
     * @see testGetCondition()
     *
     * @return array
     */
    public function dataGetCondition()
    {
        $data = [
            'testSeriouslyNestedMethod' => [
                '/* testSeriouslyNestedMethod */',
                [
                    'T_CLASS'     => '/* condition 5: nested class */',
                    'T_NAMESPACE' => '/* condition 0: namespace */',
                    'T_FUNCTION'  => '/* condition 2: function */',
                    'T_IF'        => '/* condition 1: if */',
                    'T_ELSE'      => '/* condition 3-2: else */',
                ],
            ],
            'testDeepestNested'         => [
                '/* testDeepestNested */',
                [
                    'T_CLASS'      => '/* condition 5: nested class */',
                    'T_ANON_CLASS' => '/* condition 11-1: nested anonymous class */',
                    'T_NAMESPACE'  => '/* condition 0: namespace */',
                    'T_FUNCTION'   => '/* condition 2: function */',
                    'T_CLOSURE'    => '/* condition 13: closure */',
                    'T_IF'         => '/* condition 1: if */',
                    'T_SWITCH'     => '/* condition 7: switch */',
                    'T_CASE'       => '/* condition 8a: case */',
                    'T_WHILE'      => '/* condition 9: while */',
                    'T_ELSE'       => '/* condition 3-2: else */',
                ],
            ],
            'testInException'           => [
                '/* testInException */',
                [
                    'T_CLASS'     => '/* condition 5: nested class */',
                    'T_NAMESPACE' => '/* condition 0: namespace */',
                    'T_FUNCTION'  => '/* condition 2: function */',
                    'T_IF'        => '/* condition 1: if */',
                    'T_SWITCH'    => '/* condition 7: switch */',
                    'T_CASE'      => '/* condition 8a: case */',
                    'T_WHILE'     => '/* condition 9: while */',
                    'T_ELSE'      => '/* condition 3-2: else */',
                    'T_FOREACH'   => '/* condition 10-3: foreach */',
                    'T_CATCH'     => '/* condition 11-3: catch */',
                ],
            ],
            'testInDefault'             => [
                '/* testInDefault */',
                [
                    'T_CLASS'     => '/* condition 5: nested class */',
                    'T_NAMESPACE' => '/* condition 0: namespace */',
                    'T_FUNCTION'  => '/* condition 2: function */',
                    'T_IF'        => '/* condition 1: if */',
                    'T_SWITCH'    => '/* condition 7: switch */',
                    'T_DEFAULT'   => '/* condition 8b: default */',
                    'T_ELSE'      => '/* condition 3-2: else */',
                ],
            ],
        ];

        // Set up the data for the reversed results.
        $reversed         = $data['testSeriouslyNestedMethod'][1];
        $reversed['T_IF'] = '/* condition 4: if */';
        $data['testSeriouslyNestedMethod'][] = $reversed;

        $reversed = $data['testDeepestNested'][1];
        $reversed['T_FUNCTION']      = '/* condition 12: nested anonymous class method */';
        $reversed['T_IF']            = '/* condition 10-1: if */';
        $data['testDeepestNested'][] = $reversed;

        $reversed = $data['testInException'][1];
        $reversed['T_FUNCTION']    = '/* condition 6: class method */';
        $reversed['T_IF']          = '/* condition 4: if */';
        $data['testInException'][] = $reversed;

        $reversed = $data['testInDefault'][1];
        $reversed['T_FUNCTION']  = '/* condition 6: class method */';
        $reversed['T_IF']        = '/* condition 4: if */';
        $data['testInDefault'][] = $reversed;

        return $data;

    }//end dataGetCondition()


    /**
     * Test retrieving a specific condition from a token's "conditions" array,
     * with multiple allowed possibilities.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::getCondition
     *
     * @return void
     */
    public function testGetConditionMultipleTypes()
    {
        $stackPtr = $this->testTokens['/* testInException */'];

        $result = Conditions::getCondition(self::$phpcsFile, $stackPtr, [T_DO, T_FOR]);
        $this->assertFalse(
            $result,
            'Failed asserting that "testInException" does not have a "do" nor a "for" condition'
        );

        $result = Conditions::getCondition(self::$phpcsFile, $stackPtr, [T_DO, T_FOR, T_FOREACH]);
        $this->assertSame(
            $this->markerTokens['/* condition 10-3: foreach */'],
            $result,
            'Failed asserting that "testInException" has a foreach condition based on the types "do", "for" and "foreach"'
        );

        $stackPtr = $this->testTokens['/* testDeepestNested */'];

        $result = Conditions::getCondition(self::$phpcsFile, $stackPtr, [T_INTERFACE, T_TRAIT]);
        $this->assertFalse(
            $result,
            'Failed asserting that "testDeepestNested" does not have an interface nor a trait condition'
        );

        $result = Conditions::getCondition(self::$phpcsFile, $stackPtr, Tokens::$ooScopeTokens);
        $this->assertSame(
            $this->markerTokens['/* condition 5: nested class */'],
            $result,
            'Failed asserting that "testDeepestNested" has a class condition based on the OO Scope token types'
        );

    }//end testGetConditionMultipleTypes()


    /**
     * Test whether a token has a condition of a certain type.
     *
     * @param string $testMarker      The comment which prefaces the target token in the test file.
     * @param array  $expectedResults Array with the condition token type to search for as key
     *                                and the expected result as a value.
     *
     * @dataProvider dataHasCondition
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Conditions::hasCondition
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Conditions::getCondition
     *
     * @return void
     */
    public function testHasCondition($testMarker, $expectedResults)
    {
        $stackPtr = $this->testTokens[$testMarker];

        // Add expected results for all test markers not listed in the data provider.
        $expectedResults += $this->conditionDefaults;

        foreach ($expectedResults as $conditionType => $expected) {
            $result = Conditions::hasCondition(self::$phpcsFile, $stackPtr, constant($conditionType));
            $this->assertSame(
                $expected,
                $result,
                "Assertion failed for test marker '{$testMarker}' with condition {$conditionType}"
            );
        }

    }//end testHasCondition()


    /**
     * Data Provider.
     *
     * Only list the "true" conditions in the $results array.
     * All other potential conditions will automatically also be tested
     * and will expect "false" as a result.
     *
     * @see testHasCondition()
     *
     * @return array
     */
    public function dataHasCondition()
    {
        return [
            'testSeriouslyNestedMethod' => [
                '/* testSeriouslyNestedMethod */',
                [
                    'T_CLASS'     => true,
                    'T_NAMESPACE' => true,
                    'T_FUNCTION'  => true,
                    'T_IF'        => true,
                    'T_ELSE'      => true,
                ],
            ],
            'testDeepestNested'         => [
                '/* testDeepestNested */',
                [
                    'T_CLASS'      => true,
                    'T_ANON_CLASS' => true,
                    'T_NAMESPACE'  => true,
                    'T_FUNCTION'   => true,
                    'T_CLOSURE'    => true,
                    'T_IF'         => true,
                    'T_SWITCH'     => true,
                    'T_CASE'       => true,
                    'T_WHILE'      => true,
                    'T_ELSE'       => true,
                ],
            ],
            'testInException'           => [
                '/* testInException */',
                [
                    'T_CLASS'     => true,
                    'T_NAMESPACE' => true,
                    'T_FUNCTION'  => true,
                    'T_IF'        => true,
                    'T_SWITCH'    => true,
                    'T_CASE'      => true,
                    'T_WHILE'     => true,
                    'T_ELSE'      => true,
                    'T_FOREACH'   => true,
                    'T_CATCH'     => true,
                ],
            ],
            'testInDefault'             => [
                '/* testInDefault */',
                [
                    'T_CLASS'     => true,
                    'T_NAMESPACE' => true,
                    'T_FUNCTION'  => true,
                    'T_IF'        => true,
                    'T_SWITCH'    => true,
                    'T_DEFAULT'   => true,
                    'T_ELSE'      => true,
                ],
            ],
        ];

    }//end dataHasCondition()


    /**
     * Test whether a token has a condition of a certain type, with multiple allowed possibilities.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::hasCondition
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::getCondition
     *
     * @return void
     */
    public function testHasConditionMultipleTypes()
    {
        $stackPtr = $this->testTokens['/* testInException */'];

        $result = Conditions::hasCondition(self::$phpcsFile, $stackPtr, [T_TRY, T_FINALLY]);
        $this->assertFalse(
            $result,
            'Failed asserting that "testInException" does not have a "try" nor a "finally" condition'
        );

        $result = Conditions::hasCondition(self::$phpcsFile, $stackPtr, [T_TRY, T_CATCH, T_FINALLY]);
        $this->assertTrue(
            $result,
            'Failed asserting that "testInException" has a "try", "catch" or "finally" condition'
        );

        $stackPtr = $this->testTokens['/* testSeriouslyNestedMethod */'];

        $result = Conditions::hasCondition(self::$phpcsFile, $stackPtr, [T_ANON_CLASS, T_CLOSURE]);
        $this->assertFalse(
            $result,
            'Failed asserting that "testSeriouslyNestedMethod" does not have an anonymous class nor a closure condition'
        );

        $result = Conditions::hasCondition(self::$phpcsFile, $stackPtr, Tokens::$ooScopeTokens);
        $this->assertTrue(
            $result,
            'Failed asserting that "testSeriouslyNestedMethod" has an OO Scope token condition'
        );

    }//end testHasConditionMultipleTypes()


    /**
     * Test retrieving the first condition token pointer, in general and of specific types.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataGetFirstCondition
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Conditions::getFirstCondition
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Conditions::getCondition
     *
     * @return void
     */
    public function testGetFirstCondition($testMarker)
    {
        $stackPtr = $this->testTokens[$testMarker];

        $result = Conditions::getFirstCondition(self::$phpcsFile, $stackPtr);
        $this->assertSame($this->markerTokens['/* condition 0: namespace */'], $result);

        $result = Conditions::getFirstCondition(self::$phpcsFile, $stackPtr, T_IF);
        $this->assertSame($this->markerTokens['/* condition 1: if */'], $result);

        $result = Conditions::getFirstCondition(self::$phpcsFile, $stackPtr, Tokens::$ooScopeTokens);
        $this->assertSame($this->markerTokens['/* condition 5: nested class */'], $result);

        $result = Conditions::getFirstCondition(self::$phpcsFile, $stackPtr, [T_ELSEIF]);
        $this->assertFalse($result);

    }//end testGetFirstCondition()


    /**
     * Data provider. Pass the markers for the test tokens on.
     *
     * @see testGetFirstCondition()
     *
     * @return array
     */
    public function dataGetFirstCondition()
    {
        $data = [];
        foreach (self::$testTargets as $marker) {
            $data[] = [$marker];
        }

        return $data;

    }//end dataGetFirstCondition()


    /**
     * Test retrieving the last condition token pointer, in general and of specific types.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The marker for the pointers to the expected condition
     *                           results for the pre-set tests.
     *
     * @dataProvider dataGetLastCondition
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Conditions::getLastCondition
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Conditions::getCondition
     *
     * @return void
     */
    public function testGetLastCondition($testMarker, $expected)
    {
        $stackPtr = $this->testTokens[$testMarker];

        $result = Conditions::getLastCondition(self::$phpcsFile, $stackPtr);
        $this->assertSame($this->markerTokens[$expected['no type']], $result);

        $result = Conditions::getLastCondition(self::$phpcsFile, $stackPtr, T_IF);
        $this->assertSame($this->markerTokens[$expected['T_IF']], $result);

        $result = Conditions::getLastCondition(self::$phpcsFile, $stackPtr, Tokens::$ooScopeTokens);
        $this->assertSame($this->markerTokens[$expected['OO tokens']], $result);

        $result = Conditions::getLastCondition(self::$phpcsFile, $stackPtr, [T_FINALLY]);
        $this->assertFalse($result);

    }//end testGetLastCondition()


    /**
     * Data provider.
     *
     * @see testGetLastCondition()
     *
     * @return array
     */
    public function dataGetLastCondition()
    {
        return [
            'testSeriouslyNestedMethod' => [
                '/* testSeriouslyNestedMethod */',
                [
                    'no type'   => '/* condition 5: nested class */',
                    'T_IF'      => '/* condition 4: if */',
                    'OO tokens' => '/* condition 5: nested class */',
                ],
            ],
            'testDeepestNested'         => [
                '/* testDeepestNested */',
                [
                    'no type'   => '/* condition 13: closure */',
                    'T_IF'      => '/* condition 10-1: if */',
                    'OO tokens' => '/* condition 11-1: nested anonymous class */',
                ],
            ],
            'testInException'           => [
                '/* testInException */',
                [
                    'no type'   => '/* condition 11-3: catch */',
                    'T_IF'      => '/* condition 4: if */',
                    'OO tokens' => '/* condition 5: nested class */',
                ],
            ],
            'testInDefault'             => [
                '/* testInDefault */',
                [
                    'no type'   => '/* condition 8b: default */',
                    'T_IF'      => '/* condition 4: if */',
                    'OO tokens' => '/* condition 5: nested class */',
                ],
            ],
        ];

    }//end dataGetLastCondition()


}//end class
