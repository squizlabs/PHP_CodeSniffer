<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:getClassProperties method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2022 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class GetClassPropertiesTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a non class token is passed.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $tokenType  The type of token to look for after the marker.
     *
     * @dataProvider dataNotAClassException
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_CLASS
     *
     * @return void
     */
    public function testNotAClassException($testMarker, $tokenType)
    {
        $target = $this->getTargetToken($testMarker, $tokenType);
        self::$phpcsFile->getClassProperties($target);

    }//end testNotAClassException()


    /**
     * Data provider.
     *
     * @see testNotAClassException() For the array format.
     *
     * @return array
     */
    public function dataNotAClassException()
    {
        return [
            'interface'  => [
                '/* testNotAClass */',
                \T_INTERFACE,
            ],
            'anon-class' => [
                '/* testAnonClass */',
                \T_ANON_CLASS,
            ],
            'enum'       => [
                '/* testEnum */',
                \T_ENUM,
            ],
        ];

    }//end dataNotAClassException()


    /**
     * Test retrieving the properties for a class declaration.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   Expected function output.
     *
     * @dataProvider dataGetClassProperties
     *
     * @return void
     */
    public function testGetClassProperties($testMarker, $expected)
    {
        $class  = $this->getTargetToken($testMarker, \T_CLASS);
        $result = self::$phpcsFile->getClassProperties($class);
        $this->assertSame($expected, $result);

    }//end testGetClassProperties()


    /**
     * Data provider.
     *
     * @see testGetClassProperties() For the array format.
     *
     * @return array
     */
    public function dataGetClassProperties()
    {
        return [
            'no-properties'               => [
                '/* testClassWithoutProperties */',
                [
                    'is_abstract' => false,
                    'is_final'    => false,
                    'is_readonly' => false,
                ],
            ],
            'abstract'                    => [
                '/* testAbstractClass */',
                [
                    'is_abstract' => true,
                    'is_final'    => false,
                    'is_readonly' => false,
                ],
            ],
            'final'                       => [
                '/* testFinalClass */',
                [
                    'is_abstract' => false,
                    'is_final'    => true,
                    'is_readonly' => false,
                ],
            ],
            'readonly'                    => [
                '/* testReadonlyClass */',
                [
                    'is_abstract' => false,
                    'is_final'    => false,
                    'is_readonly' => true,
                ],
            ],
            'final-readonly'              => [
                '/* testFinalReadonlyClass */',
                [
                    'is_abstract' => false,
                    'is_final'    => true,
                    'is_readonly' => true,
                ],
            ],
            'readonly-final'              => [
                '/* testReadonlyFinalClass */',
                [
                    'is_abstract' => false,
                    'is_final'    => true,
                    'is_readonly' => true,
                ],
            ],
            'abstract-readonly'           => [
                '/* testAbstractReadonlyClass */',
                [
                    'is_abstract' => true,
                    'is_final'    => false,
                    'is_readonly' => true,
                ],
            ],
            'readonly-abstract'           => [
                '/* testReadonlyAbstractClass */',
                [
                    'is_abstract' => true,
                    'is_final'    => false,
                    'is_readonly' => true,
                ],
            ],
            'comments-and-new-lines'      => [
                '/* testWithCommentsAndNewLines */',
                [
                    'is_abstract' => true,
                    'is_final'    => false,
                    'is_readonly' => false,
                ],
            ],
            'no-properties-with-docblock' => [
                '/* testWithDocblockWithoutProperties */',
                [
                    'is_abstract' => false,
                    'is_final'    => false,
                    'is_readonly' => false,
                ],
            ],
            'abstract-final-parse-error'  => [
                '/* testParseErrorAbstractFinal */',
                [
                    'is_abstract' => true,
                    'is_final'    => true,
                    'is_readonly' => false,
                ],
            ],
        ];

    }//end dataGetClassProperties()


}//end class
