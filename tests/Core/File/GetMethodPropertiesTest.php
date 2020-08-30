<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:getMethodProperties method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class GetMethodPropertiesTest extends AbstractMethodUnitTest
{


    /**
     * Test a basic function.
     *
     * @return void
     */
    public function testBasicFunction()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testBasicFunction()


    /**
     * Test a function with a return type.
     *
     * @return void
     */
    public function testReturnFunction()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => 'array',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnFunction()


    /**
     * Test a closure used as a function argument.
     *
     * @return void
     */
    public function testNestedClosure()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => 'int',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNestedClosure()


    /**
     * Test a basic method.
     *
     * @return void
     */
    public function testBasicMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testBasicMethod()


    /**
     * Test a private static method.
     *
     * @return void
     */
    public function testPrivateStaticMethod()
    {
        $expected = [
            'scope'                => 'private',
            'scope_specified'      => true,
            'return_type'          => '',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => true,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPrivateStaticMethod()


    /**
     * Test a basic final method.
     *
     * @return void
     */
    public function testFinalMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => true,
            'return_type'          => '',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => true,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testFinalMethod()


    /**
     * Test a protected method with a return type.
     *
     * @return void
     */
    public function testProtectedReturnMethod()
    {
        $expected = [
            'scope'                => 'protected',
            'scope_specified'      => true,
            'return_type'          => 'int',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testProtectedReturnMethod()


    /**
     * Test a public method with a return type.
     *
     * @return void
     */
    public function testPublicReturnMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => true,
            'return_type'          => 'array',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPublicReturnMethod()


    /**
     * Test a public method with a nullable return type.
     *
     * @return void
     */
    public function testNullableReturnMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => true,
            'return_type'          => '?array',
            'nullable_return_type' => true,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNullableReturnMethod()


    /**
     * Test a public method with a nullable return type.
     *
     * @return void
     */
    public function testMessyNullableReturnMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => true,
            'return_type'          => '?array',
            'nullable_return_type' => true,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testMessyNullableReturnMethod()


    /**
     * Test a method with a namespaced return type.
     *
     * @return void
     */
    public function testReturnNamespace()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '\MyNamespace\MyClass',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnNamespace()


    /**
     * Test a method with a messy namespaces return type.
     *
     * @return void
     */
    public function testReturnMultilineNamespace()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '\MyNamespace\MyClass\Foo',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnMultilineNamespace()


    /**
     * Test a basic abstract method.
     *
     * @return void
     */
    public function testAbstractMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '',
            'nullable_return_type' => false,
            'is_abstract'          => true,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => false,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testAbstractMethod()


    /**
     * Test an abstract method with a return type.
     *
     * @return void
     */
    public function testAbstractReturnMethod()
    {
        $expected = [
            'scope'                => 'protected',
            'scope_specified'      => true,
            'return_type'          => 'bool',
            'nullable_return_type' => false,
            'is_abstract'          => true,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => false,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testAbstractReturnMethod()


    /**
     * Test a basic interface method.
     *
     * @return void
     */
    public function testInterfaceMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => false,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testInterfaceMethod()


    /**
     * Test a static arrow function.
     *
     * @return void
     */
    public function testArrowFunction()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => 'int',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => true,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrowFunction()


    /**
     * Test a function with return type "static".
     *
     * @return void
     */
    public function testReturnTypeStatic()
    {
        $expected = [
            'scope'                => 'private',
            'scope_specified'      => true,
            'return_type'          => 'static',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnTypeStatic()


    /**
     * Test a function with return type "mixed".
     *
     * @return void
     */
    public function testPHP8MixedTypeHint()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => 'mixed',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8MixedTypeHint()


    /**
     * Test a function with return type "mixed" and nullability.
     *
     * @return void
     */
    public function testPHP8MixedTypeHintNullable()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '?mixed',
            'nullable_return_type' => true,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8MixedTypeHintNullable()


    /**
     * Test a function with return type using the namespace operator.
     *
     * @return void
     */
    public function testNamespaceOperatorTypeHint()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '?namespace\Name',
            'nullable_return_type' => true,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getMethodPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNamespaceOperatorTypeHint()


    /**
     * Test helper.
     *
     * @param string $commentString The comment which preceeds the test.
     * @param array  $expected      The expected function output.
     *
     * @return void
     */
    private function getMethodPropertiesTestHelper($commentString, $expected)
    {
        $function = $this->getTargetToken($commentString, [T_FUNCTION, T_CLOSURE, T_FN]);
        $found    = self::$phpcsFile->getMethodProperties($function);

        $this->assertArraySubset($expected, $found, true);

    }//end getMethodPropertiesTestHelper()


}//end class
