<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations::findExtendedClassName() method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\ObjectDeclarations;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations;

class FindExtendedClassNameTest extends AbstractMethodUnitTest
{


    /**
     * Test retrieving the name of the class being extended by another class
     * (or interface).
     *
     * @param string       $testMarker The comment which prefaces the target token in the test file.
     * @param string|false $expected   Expected function output.
     *
     * @dataProvider dataExtendedClass
     * @covers       \PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations::findExtendedClassName
     *
     * @return void
     */
    public function testFindExtendedClassName($testMarker, $expected)
    {
        $OOToken = $this->getTargetToken($testMarker, [T_CLASS, T_ANON_CLASS, T_INTERFACE]);
        $result  = ObjectDeclarations::findExtendedClassName(self::$phpcsFile, $OOToken);
        $this->assertSame($expected, $result);

    }//end testFindExtendedClassName()


    /**
     * Data provider for the FindExtendedClassName test.
     *
     * @see testFindExtendedClassName()
     *
     * @return array
     */
    public function dataExtendedClass()
    {
        return [
            [
                '/* testExtendedClass */',
                'testFECNClass',
            ],
            [
                '/* testNamespacedClass */',
                '\PHP_CodeSniffer\Tests\Core\File\testFECNClass',
            ],
            [
                '/* testNonExtendedClass */',
                false,
            ],
            [
                '/* testInterface */',
                false,
            ],
            [
                '/* testInterfaceThatExtendsInterface */',
                'testFECNInterface',
            ],
            [
                '/* testInterfaceThatExtendsFQCNInterface */',
                '\PHP_CodeSniffer\Tests\Core\File\testFECNInterface',
            ],
            [
                '/* testNestedExtendedClass */',
                false,
            ],
            [
                '/* testNestedExtendedAnonClass */',
                'testFECNAnonClass',
            ],
            [
                '/* testClassThatExtendsAndImplements */',
                'testFECNClass',
            ],
            [
                '/* testClassThatImplementsAndExtends */',
                'testFECNClass',
            ],
        ];

    }//end dataExtendedClass()


}//end class
