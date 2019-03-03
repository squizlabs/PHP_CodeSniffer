<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations::getClassProperties() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\ObjectDeclarations;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations;

class GetClassPropertiesTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a non class token is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_CLASS
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations::getClassProperties
     *
     * @return void
     */
    public function testNotAClassException()
    {
        $interface = $this->getTargetToken('/* testNotAClass */', T_INTERFACE);
        $result    = ObjectDeclarations::getClassProperties(self::$phpcsFile, $interface);

    }//end testNotAClassException()


    /**
     * Test the retrieving the properties for a class declaration.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   Expected function output.
     *
     * @dataProvider dataGetClassProperties
     * @covers       \PHP_CodeSniffer\Util\Sniffs\ObjectDeclarations::getClassProperties
     *
     * @return void
     */
    public function testGetClassProperties($testMarker, $expected)
    {
        $class  = $this->getTargetToken($testMarker, T_CLASS);
        $result = ObjectDeclarations::getClassProperties(self::$phpcsFile, $class);
        $this->assertSame($expected, $result);

    }//end testGetClassProperties()


    /**
     * Data provider.
     *
     * @see testGetClassProperties()
     *
     * @return array
     */
    public function dataGetClassProperties()
    {
        return [
            [
                '/* testClassWithoutProperties */',
                [
                    'is_abstract' => false,
                    'is_final'    => false,
                ],
            ],
            [
                '/* testAbstractClass */',
                [
                    'is_abstract' => true,
                    'is_final'    => false,
                ],
            ],
            [
                '/* testFinalClass */',
                [
                    'is_abstract' => false,
                    'is_final'    => true,
                ],
            ],
            [
                '/* testWithCommentsAndNewLines */',
                [
                    'is_abstract' => true,
                    'is_final'    => false,
                ],
            ],
            [
                '/* testWithDocblockWithoutProperties */',
                [
                    'is_abstract' => false,
                    'is_final'    => false,
                ],
            ],
        ];

    }//end dataGetClassProperties()


}//end class
