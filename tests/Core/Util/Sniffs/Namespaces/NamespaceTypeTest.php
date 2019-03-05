<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Namespaces::isDeclaration(),
 * \PHP_CodeSniffer\Util\Sniffs\Namespaces::isOperator() and the.
 * \PHP_CodeSniffer\Util\Sniffs\Namespaces::getType() methods.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Namespaces;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Namespaces;

class NamespaceTypeTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when passing a non-existent token pointer.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_NAMESPACE
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Namespaces::getType
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = Namespaces::getType(self::$phpcsFile, 100000);

    }//end testNonExistentToken()


    /**
     * Test receiving an expected exception when passing a non T_NAMESPACE token.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_NAMESPACE
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Namespaces::getType
     *
     * @return void
     */
    public function testNonNamespaceToken()
    {
        $result = Namespaces::getType(self::$phpcsFile, 0);

    }//end testNonNamespaceToken()


    /**
     * Test whether a T_NAMESPACE token is used as the keyword for a namespace declaration.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected output for the functions.
     *
     * @dataProvider dataNamespaceType
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Namespaces::isDeclaration
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Namespaces::getType
     *
     * @return void
     */
    public function testIsDeclaration($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_NAMESPACE);
        $result   = Namespaces::isDeclaration(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['declaration'], $result);

    }//end testIsDeclaration()


    /**
     * Test whether a T_NAMESPACE token is used as an operator.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected output for the functions.
     *
     * @dataProvider dataNamespaceType
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Namespaces::isOperator
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Namespaces::getType
     *
     * @return void
     */
    public function testIsOperator($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_NAMESPACE);
        $result   = Namespaces::isOperator(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['operator'], $result);

    }//end testIsOperator()


    /**
     * Data provider.
     *
     * @see testIsDeclaration()
     * @see testIsOperator()
     *
     * @return array
     */
    public function dataNamespaceType()
    {
        return [
            [
                '/* testNamespaceDeclaration */',
                [
                    'declaration' => true,
                    'operator'    => false,
                ],
            ],
            [
                '/* testNamespaceDeclarationWithComment */',
                [
                    'declaration' => true,
                    'operator'    => false,
                ],
            ],
            [
                '/* testNamespaceDeclarationScoped */',
                [
                    'declaration' => true,
                    'operator'    => false,
                ],
            ],
            [
                '/* testNamespaceOperator */',
                [
                    'declaration' => false,
                    'operator'    => true,
                ],
            ],
            [
                '/* testNamespaceOperatorWithAnnotation */',
                [
                    'declaration' => false,
                    'operator'    => true,
                ],
            ],
            [
                '/* testParseError */',
                [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],
            [
                '/* testLiveCoding */',
                [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],
        ];

    }//end dataNamespaceType()


}//end class
