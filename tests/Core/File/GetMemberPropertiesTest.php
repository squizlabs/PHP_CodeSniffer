<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File::getMemberProperties method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class GetMemberPropertiesTest extends AbstractMethodUnitTest
{


    /**
     * Test the getMemberProperties() method.
     *
     * @param string $identifier Comment which precedes the test case.
     * @param bool   $expected   Expected function output.
     *
     * @dataProvider dataGetMemberProperties
     *
     * @return void
     */
    public function testGetMemberProperties($identifier, $expected)
    {
        $variable = $this->getTargetToken($identifier, T_VARIABLE);
        $result   = self::$phpcsFile->getMemberProperties($variable);

        $this->assertArraySubset($expected, $result, true);

    }//end testGetMemberProperties()


    /**
     * Data provider for the GetMemberProperties test.
     *
     * @see testGetMemberProperties()
     *
     * @return array
     */
    public function dataGetMemberProperties()
    {
        return [
            [
                '/* testVar */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testVarType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                    'type'            => '?int',
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testPublic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPublicType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'string',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testProtected */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testProtectedType */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'bool',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPrivate */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPrivateType */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'array',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testStatic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testStaticType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                    'type'            => '?string',
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testStaticVar */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testVarStatic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPublicStatic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testProtectedStatic */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPrivateStatic */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPublicStaticWithDocblock */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testProtectedStaticWithDocblock */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPrivateStaticWithDocblock */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupType 1 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'float',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupType 2 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => 'float',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupNullableType 1 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '?string',
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testGroupNullableType 2 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '?string',
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testNoPrefix */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupProtectedStatic 1 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupProtectedStatic 2 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupProtectedStatic 3 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 1 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 2 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 3 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 4 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 5 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 6 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testGroupPrivate 7 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testMessyNullableType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '?array',
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testNamespaceType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '\MyNamespace\MyClass',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testNullableNamespaceType 1 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '?ClassName',
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testNullableNamespaceType 2 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '?Folder\ClassName',
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testMultilineNamespaceType */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '\MyNamespace\MyClass\Foo',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPropertyAfterMethod */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testInterfaceProperty */',
                [],
            ],
            [
                '/* testNestedProperty 1 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testNestedProperty 2 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPHP8MixedTypeHint */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                    'type'            => 'miXed',
                    'nullable_type'   => false,
                ],
            ],
            [
                '/* testPHP8MixedTypeHintNullable */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '?mixed',
                    'nullable_type'   => true,
                ],
            ],
            [
                '/* testNamespaceOperatorTypeHint */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                    'type'            => '?namespace\Name',
                    'nullable_type'   => true,
                ],
            ],
        ];

    }//end dataGetMemberProperties()


    /**
     * Test receiving an expected exception when a non property is passed.
     *
     * @param string $identifier Comment which precedes the test case.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr is not a class member var
     *
     * @dataProvider dataNotClassProperty
     *
     * @return void
     */
    public function testNotClassPropertyException($identifier)
    {
        $variable = $this->getTargetToken($identifier, T_VARIABLE);
        $result   = self::$phpcsFile->getMemberProperties($variable);

    }//end testNotClassPropertyException()


    /**
     * Data provider for the NotClassPropertyException test.
     *
     * @see testNotClassPropertyException()
     *
     * @return array
     */
    public function dataNotClassProperty()
    {
        return [
            ['/* testMethodParam */'],
            ['/* testImportedGlobal */'],
            ['/* testLocalVariable */'],
            ['/* testGlobalVariable */'],
            ['/* testNestedMethodParam 1 */'],
            ['/* testNestedMethodParam 2 */'],
        ];

    }//end dataNotClassProperty()


    /**
     * Test receiving an expected exception when a non variable is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_VARIABLE
     *
     * @return void
     */
    public function testNotAVariableException()
    {
        $next   = $this->getTargetToken('/* testNotAVariable */', T_RETURN);
        $result = self::$phpcsFile->getMemberProperties($next);

    }//end testNotAVariableException()


}//end class
