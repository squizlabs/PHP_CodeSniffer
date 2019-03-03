<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations::findExtendedInterfaceNames() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2018-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\ObjectDeclarations;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations;

class FindExtendedInterfaceNamesTest extends AbstractMethodUnitTest
{


    /**
     * Test retrieving the names of the interfaces being extended by another interface.
     *
     * @param string      $testMarker The comment which prefaces the target token in the test file.
     * @param array|false $expected   Expected function output.
     *
     * @dataProvider dataExtendedInterface
     * @covers       \PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations::findExtendedInterfaceNames
     * @covers       \PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations::findExtendedImplemented
     *
     * @return void
     */
    public function testFindExtendedInterfaceNames($testMarker, $expected)
    {
        $interface = $this->getTargetToken($testMarker, [T_INTERFACE]);
        $result    = ObjectDeclarations::findExtendedInterfaceNames(self::$phpcsFile, $interface);
        $this->assertSame($expected, $result);

    }//end testFindExtendedInterfaceNames()


    /**
     * Data provider.
     *
     * @see testFindExtendedInterfaceNames()
     *
     * @return array
     */
    public function dataExtendedInterface()
    {
        return [
            [
                '/* testInterface */',
                false,
            ],
            [
                '/* testExtendedInterface */',
                ['testFEINInterface'],
            ],
            [
                '/* testMultiExtendedInterface */',
                [
                    'testFEINInterface',
                    'testFEINInterface2',
                ],
            ],
            [
                '/* testNamespacedInterface */',
                ['\PHP_CodeSniffer\Tests\Core\File\testFEINInterface'],
            ],
            [
                '/* testMultiNamespacedInterface */',
                [
                    '\PHP_CodeSniffer\Tests\Core\File\testFEINInterface',
                    '\PHP_CodeSniffer\Tests\Core\File\testFEINInterface2',
                ],
            ],
            [
                '/* testMultiExtendedInterfaceWithComment */',
                [
                    'testFEINInterface',
                    '\PHP_CodeSniffer\Tests\Core\File\testFEINInterface2',
                    '\testFEINInterface3',
                ],
            ],
        ];

    }//end dataExtendedInterface()


}//end class
