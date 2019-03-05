<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Namespaces::findNamespacePtr() and
 * \PHP_CodeSniffer\Util\Sniffs\Namespaces::determineNamespace() methods.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Namespaces;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Namespaces;

class DetermineNamespaceTest extends AbstractMethodUnitTest
{


    /**
     * Test that false is returned when an invalid token is passed.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Namespaces::findNamespacePtr
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        $this->assertFalse(Namespaces::findNamespacePtr(self::$phpcsFile, 100000));

    }//end testInvalidTokenPassed()


    /**
     * Test finding the correct namespace token for an arbitrary token in a file.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected output for the functions.
     *
     * @dataProvider dataDetermineNamespace
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Namespaces::findNamespacePtr
     *
     * @return void
     */
    public function testFindNamespacePtr($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_ECHO);

        if ($expected['ptr'] !== false) {
            $expected['ptr'] = $this->getTargetToken($expected['ptr'], T_NAMESPACE);
        }

        $result = Namespaces::findNamespacePtr(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['ptr'], $result);

    }//end testFindNamespacePtr()


    /**
     * Test retrieving the applicabe namespace name for an arbitrary token in a file.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected output for the functions.
     *
     * @dataProvider dataDetermineNamespace
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Namespaces::determineNamespace
     *
     * @return void
     */
    public function testDetermineNamespace($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_ECHO);
        $result   = Namespaces::determineNamespace(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['name'], $result);

    }//end testDetermineNamespace()


    /**
     * Data provider.
     *
     * @see testDetermineNamespace()
     *
     * @return array
     */
    public function dataDetermineNamespace()
    {
        return [
            [
                '/* testNoNamespace */',
                [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            [
                '/* testNoNamespaceNested */',
                [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            [
                '/* testNonScopedNamedNamespace1 */',
                [
                    'ptr'  => '/* Non-scoped named namespace 1 */',
                    'name' => 'Vendor\Package\Baz',
                ],
            ],
            [
                '/* testNonScopedNamedNamespace1Nested */',
                [
                    'ptr'  => '/* Non-scoped named namespace 1 */',
                    'name' => 'Vendor\Package\Baz',
                ],
            ],
            [
                '/* testGlobalNamespaceScoped */',
                [
                    'ptr'  => '/* Scoped global namespace */',
                    'name' => '',
                ],
            ],
            [
                '/* testGlobalNamespaceScopedNested */',
                [
                    'ptr'  => '/* Scoped global namespace */',
                    'name' => '',
                ],
            ],
            [
                '/* testNoNamespaceAfterScoped */',
                [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            [
                '/* testNoNamespaceNestedAfterScoped */',
                [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            [
                '/* testNamedNamespaceScoped */',
                [
                    'ptr'  => '/* Scoped named namespace */',
                    'name' => 'Vendor\Package\Foo',
                ],
            ],
            [
                '/* testNamedNamespaceScopedNested */',
                [
                    'ptr'  => '/* Scoped named namespace */',
                    'name' => 'Vendor\Package\Foo',
                ],
            ],
            [
                '/* testNonScopedGlobalNamespace */',
                [
                    'ptr'  => '/* Non-scoped global namespace */',
                    'name' => '',
                ],
            ],
            [
                '/* testNonScopedGlobalNamespaceNested */',
                [
                    'ptr'  => '/* Non-scoped global namespace */',
                    'name' => '',
                ],
            ],
            [
                '/* testNonScopedNamedNamespace2 */',
                [
                    'ptr'  => '/* Non-scoped named namespace 2 */',
                    'name' => 'Vendor\Package\Foz',
                ],
            ],
            [
                '/* testNonScopedNamedNamespace2Nested */',
                [
                    'ptr'  => '/* Non-scoped named namespace 2 */',
                    'name' => 'Vendor\Package\Foz',
                ],
            ],
        ];

    }//end dataDetermineNamespace()


    /**
     * Test returning an empty string if the namespace could not be determined (parse error).
     *
     * @dataProvider dataDetermineNamespace
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Namespaces::findNamespacePtr
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Namespaces::determineNamespace
     *
     * @return void
     */
    public function testFallbackToEmptyString()
    {
        $stackPtr = $this->getTargetToken('/* testParseError */', T_COMMENT, '/* comment */');

        $expected = $this->getTargetToken('/* testParseError */', T_NAMESPACE);
        $result   = Namespaces::findNamespacePtr(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

        $result = Namespaces::determineNamespace(self::$phpcsFile, $stackPtr, false);
        $this->assertSame('', $result);

    }//end testFallbackToEmptyString()


}//end class
