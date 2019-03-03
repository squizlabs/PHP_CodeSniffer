<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isPHPDoubleUnderscoreMethodName() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\FunctionDeclarations;

use PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations;
use PHPUnit\Framework\TestCase;

class IsPHPDoubleUnderscoreMethodNameTest extends TestCase
{


    /**
     * Test valid PHP native double underscore method names.
     *
     * @param string $name The function name to test.
     *
     * @dataProvider dataIsPHPDoubleUnderscoreMethodName
     * @covers       \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isPHPDoubleUnderscoreMethodName
     *
     * @return void
     */
    public function testIsPHPDoubleUnderscoreMethodName($name)
    {
        $this->assertTrue(FunctionDeclarations::isPHPDoubleUnderscoreMethodName($name));

    }//end testIsPHPDoubleUnderscoreMethodName()


    /**
     * Test valid PHP native double underscore method names.
     *
     * @param string $name The function name to test.
     *
     * @dataProvider dataIsPHPDoubleUnderscoreMethodName
     * @covers       \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isSpecialMethodName
     *
     * @return void
     */
    public function testIsSpecialMethodName($name)
    {
        $this->assertTrue(FunctionDeclarations::isSpecialMethodName($name));

    }//end testIsSpecialMethodName()


    /**
     * Data provider.
     *
     * @see testIsPHPDoubleUnderscoreMethodName()
     *
     * @return array
     */
    public function dataIsPHPDoubleUnderscoreMethodName()
    {
        return [
            // Normal case.
            ['__doRequest'],
            ['__getCookies'],
            ['__getFunctions'],
            ['__getLastRequest'],
            ['__getLastRequestHeaders'],
            ['__getLastResponse'],
            ['__getLastResponseHeaders'],
            ['__getTypes'],
            ['__setCookie'],
            ['__setLocation'],
            ['__setSoapHeaders'],
            ['__soapCall'],

            // Uppercase et al.
            ['__DOREQUEST'],
            ['__getcookies'],
            ['__Getfunctions'],
            ['__GETLASTREQUEST'],
            ['__getlastrequestheaders'],
            ['__GetlastResponse'],
            ['__GETLASTRESPONSEHEADERS'],
            ['__GetTypes'],
            ['__SETCookie'],
            ['__sETlOCATION'],
            ['__SetSOAPHeaders'],
            ['__SOAPCall'],
        ];

    }//end dataIsPHPDoubleUnderscoreMethodName()


    /**
     * Test function names which are not valid PHP native double underscore methods.
     *
     * @param string $name The function name to test.
     *
     * @dataProvider dataIsPHPDoubleUnderscoreMethodNameFalse
     * @covers       \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isPHPDoubleUnderscoreMethodName
     *
     * @return void
     */
    public function testIsPHPDoubleUnderscoreMethodNameFalse($name)
    {
        $this->assertFalse(FunctionDeclarations::isPHPDoubleUnderscoreMethodName($name));

    }//end testIsPHPDoubleUnderscoreMethodNameFalse()


    /**
     * Test function names which are not valid PHP native double underscore methods.
     *
     * @param string $name The function name to test.
     *
     * @dataProvider dataIsPHPDoubleUnderscoreMethodNameFalse
     * @covers       \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isSpecialMethodName
     *
     * @return void
     */
    public function testIsSpecialMethodNameFalse($name)
    {
        $this->assertFalse(FunctionDeclarations::isSpecialMethodName($name));

    }//end testIsSpecialMethodNameFalse()


    /**
     * Data provider.
     *
     * @see testIsPHPDoubleUnderscoreMethodNameFalse()
     *
     * @return array
     */
    public function dataIsPHPDoubleUnderscoreMethodNameFalse()
    {
        return [
            'no_underscore'           => ['getLastResponseHeaders'],
            'single_underscore'       => ['_setLocation'],
            'triple_underscore'       => ['___getCookies'],
            'not_magic_function_name' => ['__getFirstRequestHeader'],
        ];

    }//end dataIsPHPDoubleUnderscoreMethodNameFalse()


}//end class
