<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\UseStatements::splitImportUseStatement() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\UseStatements;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\UseStatements;

class SplitImportUseStatementTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a non-supported token is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_USE
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\UseStatements::splitImportUseStatement
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        // 0 = PHP open tag.
        $result = UseStatements::splitImportUseStatement(self::$phpcsFile, 0);

    }//end testInvalidTokenPassed()


    /**
     * Test receiving an expected exception when a non-import use statement token is passed.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be an import use statement
     *
     * @dataProvider dataNonImportUseTokenPassed
     * @covers       \PHP_CodeSniffer\Util\Sniffs\UseStatements::splitImportUseStatement
     *
     * @return void
     */
    public function testNonImportUseTokenPassed($testMarker)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_USE);
        $result   = UseStatements::splitImportUseStatement(self::$phpcsFile, $stackPtr);

    }//end testNonImportUseTokenPassed()


    /**
     * Data provider.
     *
     * @see testSplitImportUseStatement()
     *
     * @return array
     */
    public function dataNonImportUseTokenPassed()
    {
        return [
            ['/* testClosureUse */'],
            ['/* testTraitUse */'],
        ];

    }//end dataNonImportUseTokenPassed()


    /**
     * Test correctly splitting a T_USE statement into individual statements.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return value of the function.
     *
     * @dataProvider dataSplitImportUseStatement
     * @covers       \PHP_CodeSniffer\Util\Sniffs\UseStatements::splitImportUseStatement
     *
     * @return void
     */
    public function testSplitImportUseStatement($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_USE);
        $result   = UseStatements::splitImportUseStatement(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testSplitImportUseStatement()


    /**
     * Data provider.
     *
     * @see testSplitImportUseStatement()
     *
     * @return array
     */
    public function dataSplitImportUseStatement()
    {
        return [
            [
                '/* testUsePlain */',
                [
                    'name'     => ['MyClass' => 'MyNamespace\MyClass'],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            [
                '/* testUsePlainAliased */',
                [
                    'name'     => ['ClassAlias' => 'MyNamespace\YourClass'],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            [
                '/* testUseMultiple */',
                [
                    'name'     => [
                        'ClassABC'   => 'Vendor\Foo\ClassA',
                        'InterfaceB' => 'Vendor\Bar\InterfaceB',
                        'ClassC'     => 'Vendor\Baz\ClassC',
                    ],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            [
                '/* testUseFunctionPlainEndsOnCloseTag */',
                [
                    'name'     => [],
                    'function' => ['myFunction' => 'MyNamespace\myFunction'],
                    'const'    => [],
                ],
            ],
            [
                '/* testUseFunctionPlainAliased */',
                [
                    'name'     => [],
                    'function' => ['FunctionAlias' => 'Vendor\YourNamespace\const\yourFunction'],
                    'const'    => [],
                ],
            ],
            [
                '/* testUseFunctionMultiple */',
                [
                    'name'     => [],
                    'function' => [
                        'sin'    => 'foo\math\sin',
                        'FooCos' => 'foo\math\cos',
                        'cosh'   => 'foo\math\cosh',
                    ],
                    'const'    => [],
                ],
            ],
            [
                '/* testUseConstPlainUppercaseConstKeyword */',
                [
                    'name'     => [],
                    'function' => [],
                    'const'    => ['MY_CONST' => 'MyNamespace\MY_CONST'],
                ],
            ],
            [
                '/* testUseConstPlainAliased */',
                [
                    'name'     => [],
                    'function' => [],
                    'const'    => ['CONST_ALIAS' => 'MyNamespace\YOUR_CONST'],
                ],
            ],
            [
                '/* testUseConstMultiple */',
                [
                    'name'     => [],
                    'function' => [],
                    'const'    => [
                        'PI'          => 'foo\math\PI',
                        'MATH_GOLDEN' => 'foo\math\GOLDEN_RATIO',
                    ],
                ],
            ],
            [
                '/* testGroupUse */',
                [
                    'name'     => [
                        'SomeClassA' => 'some\namespacing\SomeClassA',
                        'SomeClassB' => 'some\namespacing\deeper\level\SomeClassB',
                        'C'          => 'some\namespacing\another\level\SomeClassC',
                    ],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            [
                '/* testGroupUseFunctionTrailingComma */',
                [
                    'name'     => [],
                    'function' => [
                        'Msin'   => 'bar\math\Msin',
                        'BarCos' => 'bar\math\level\Mcos',
                        'Mcosh'  => 'bar\math\Mcosh',
                    ],
                    'const'    => [],
                ],
            ],
            [
                '/* testGroupUseConst */',
                [
                    'name'     => [],
                    'function' => [],
                    'const'    => [
                        'BAR_GAMMA'     => 'bar\math\BGAMMA',
                        'BGOLDEN_RATIO' => 'bar\math\BGOLDEN_RATIO',
                    ],
                ],
            ],
            [
                '/* testGroupUseMixed */',
                [
                    'name'     => [
                        'ClassName'    => 'Some\NS\ClassName',
                        'AnotherLevel' => 'Some\NS\AnotherLevel',
                    ],
                    'function' => [
                        'functionName' => 'Some\NS\SubLevel\functionName',
                        'AnotherName'  => 'Some\NS\SubLevel\AnotherName',
                    ],
                    'const'    => ['SOME_CONSTANT' => 'Some\NS\Constants\CONSTANT_NAME'],
                ],
            ],
            [
                '/* testParseError */',
                [
                    'name'     => [],
                    'function' => [],
                    'const'    => [],
                ],
            ],
        ];

    }//end dataSplitImportUseStatement()


}//end class
