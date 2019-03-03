<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations::findImplementedInterfaceNames() method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\ObjectDeclarations;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations;

class FindImplementedInterfaceNamesTest extends AbstractMethodUnitTest
{


    /**
     * Test getting a `false` result when a non-existent token is passed.
     *
     * @dataProvider dataImplementedInterface
     * @covers       \PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations::findExtendedImplemented
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = ObjectDeclarations::findImplementedInterfaceNames(self::$phpcsFile, 100000);
        $this->assertFalse($result);

    }//end testNonExistentToken()


    /**
     * Test retrieving the name(s) of the interfaces being implemented by a class.
     *
     * @param string      $testMarker The comment which prefaces the target token in the test file.
     * @param array|false $expected   Expected function output.
     *
     * @dataProvider dataImplementedInterface
     * @covers       \PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations::findImplementedInterfaceNames
     * @covers       \PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations::findExtendedImplemented
     *
     * @return void
     */
    public function testFindImplementedInterfaceNames($testMarker, $expected)
    {
        $OOToken = $this->getTargetToken($testMarker, [T_CLASS, T_ANON_CLASS, T_INTERFACE]);
        $result  = ObjectDeclarations::findImplementedInterfaceNames(self::$phpcsFile, $OOToken);
        $this->assertSame($expected, $result);

    }//end testFindImplementedInterfaceNames()


    /**
     * Data provider for the FindImplementedInterfaceNames test.
     *
     * @see testFindImplementedInterfaceNames()
     *
     * @return array
     */
    public function dataImplementedInterface()
    {
        return [
            [
                '/* testImplementedClass */',
                ['testFIINInterface'],
            ],
            [
                '/* testMultiImplementedClass */',
                [
                    'testFIINInterface',
                    'testFIINInterface2',
                ],
            ],
            [
                '/* testNamespacedClass */',
                ['\PHP_CodeSniffer\Tests\Core\File\testFIINInterface'],
            ],
            [
                '/* testNonImplementedClass */',
                false,
            ],
            [
                '/* testInterface */',
                false,
            ],
            [
                '/* testClassThatExtendsAndImplements */',
                [
                    'InterfaceA',
                    '\NameSpaced\Cat\InterfaceB',
                ],
            ],
            [
                '/* testClassThatImplementsAndExtends */',
                [
                    '\InterfaceA',
                    'InterfaceB',
                ],
            ],
            [
                '/* testImplementedClassWithComments */',
                ['\PHP_CodeSniffer\Tests\Core\File\testFIINInterface'],
            ],
        ];

    }//end dataImplementedInterface()


}//end class
