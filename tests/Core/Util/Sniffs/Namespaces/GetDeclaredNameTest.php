<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Namespaces::getDeclaredName() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Namespaces;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Namespaces;

class GetDeclaredNameTest extends AbstractMethodUnitTest
{


    /**
     * Test that false is returned when an invalid token is passed.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Namespaces::getDeclaredName
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        // Non-existent token.
        $this->assertFalse(Namespaces::getDeclaredName(self::$phpcsFile, 100000));

        // Non namespace token.
        $this->assertFalse(Namespaces::getDeclaredName(self::$phpcsFile, 0));

    }//end testInvalidTokenPassed()


    /**
     * Test retrieving the cleaned up namespace name based on a T_NAMESPACE token.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected output for the function.
     *
     * @dataProvider dataGetDeclaredName
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Namespaces::getDeclaredName
     *
     * @return void
     */
    public function testGetDeclaredNameClean($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_NAMESPACE);
        $result   = Namespaces::getDeclaredName(self::$phpcsFile, $stackPtr, true);

        $this->assertSame($expected['clean'], $result);

    }//end testGetDeclaredNameClean()


    /**
     * Test retrieving the "dirty" namespace name based on a T_NAMESPACE token.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected output for the function.
     *
     * @dataProvider dataGetDeclaredName
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Namespaces::getDeclaredName
     *
     * @return void
     */
    public function testGetDeclaredNameDirty($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_NAMESPACE);
        $result   = Namespaces::getDeclaredName(self::$phpcsFile, $stackPtr, false);

        $this->assertSame($expected['dirty'], $result);

    }//end testGetDeclaredNameDirty()


    /**
     * Data provider.
     *
     * @see testGetDeclaredName()
     *
     * @return array
     */
    public function dataGetDeclaredName()
    {
        return [
            [
                '/* testNamespaceOperator */',
                [
                    'clean' => false,
                    'dirty' => false,
                ],
            ],
            [
                '/* testGlobalNamespaceSemiColon */',
                [
                    'clean' => '',
                    'dirty' => '',
                ],
            ],
            [
                '/* testGlobalNamespaceCurlies */',
                [
                    'clean' => '',
                    'dirty' => '',
                ],
            ],
            [
                '/* testGlobalNamespaceCloseTag */',
                [
                    'clean' => '',
                    'dirty' => '',
                ],
            ],
            [
                '/* testNamespaceSemiColon */',
                [
                    'clean' => 'Vendor',
                    'dirty' => 'Vendor',
                ],
            ],
            [
                '/* testNamespaceCurlies */',
                [
                    'clean' => 'Vendor\Package\Sublevel\Deeperlevel\End',
                    'dirty' => 'Vendor\Package\Sublevel\Deeperlevel\End',
                ],
            ],
            [
                '/* testNamespaceCurliesNoSpaceAtEnd */',
                [
                    'clean' => 'Vendor\Package\Sublevel\Deeperlevel\End',
                    'dirty' => 'Vendor\Package\Sublevel\Deeperlevel\End',
                ],
            ],
            [
                '/* testNamespaceCloseTag */',
                [
                    'clean' => 'PHP_CodeSniffer\Exceptions\RuntimeException',
                    'dirty' => 'PHP_CodeSniffer\Exceptions\RuntimeException',
                ],
            ],
            [
                '/* testNamespaceCloseTagNoSpaceAtEnd */',
                [
                    'clean' => 'PHP_CodeSniffer\Exceptions\RuntimeException',
                    'dirty' => 'PHP_CodeSniffer\Exceptions\RuntimeException',
                ],
            ],
            [
                '/* testNamespaceLotsOfWhitespace */',
                [
                    'clean' => 'Vendor\Package\Sublevel\Deeperlevel\End',
                    'dirty' => 'Vendor \
    Package\
        Sublevel	\
            Deeperlevel\
                End',
                ],
            ],
            [
                '/* testNamespaceWithCommentsWhitespaceAndAnnotations */',
                [
                    'clean' => 'Vendor\Package\Sublevel\Deeperlevel\End',
                    'dirty' => 'Vendor\/*comment*/
    Package\Sublevel  \ //phpcs:ignore Standard.Category.Sniff -- for reasons.
            Deeperlevel\ // Another comment
                End',
                ],
            ],
            [
                '/* testNamespaceParseError */',
                [
                    'clean' => 'Vendor\while\Package\protected\name\try\this',
                    'dirty' => 'Vendor\while\Package\protected\name\try\this',
                ],
            ],
            [
                '/* testLiveCoding */',
                [
                    'clean' => false,
                    'dirty' => false,
                ],
            ],
        ];

    }//end dataGetDeclaredName()


}//end class
