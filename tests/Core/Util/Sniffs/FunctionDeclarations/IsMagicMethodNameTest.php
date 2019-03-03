<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isMagicMethodName() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\FunctionDeclarations;

use PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations;
use PHPUnit\Framework\TestCase;

class IsMagicMethodNameTest extends TestCase
{


    /**
     * Test valid PHP magic method names.
     *
     * @param string $name The function name to test.
     *
     * @dataProvider dataIsMagicMethodName
     * @covers       \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isMagicMethodName
     *
     * @return void
     */
    public function testIsMagicMethodName($name)
    {
        $this->assertTrue(FunctionDeclarations::isMagicMethodName($name));

    }//end testIsMagicMethodName()


    /**
     * Test valid PHP magic method names.
     *
     * @param string $name The function name to test.
     *
     * @dataProvider dataIsMagicMethodName
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
     * @see testIsMagicMethodName()
     *
     * @return array
     */
    public function dataIsMagicMethodName()
    {
        return [
            // Normal case.
            ['__construct'],
            ['__destruct'],
            ['__call'],
            ['__callStatic'],
            ['__get'],
            ['__set'],
            ['__isset'],
            ['__unset'],
            ['__sleep'],
            ['__wakeup'],
            ['__toString'],
            ['__set_state'],
            ['__clone'],
            ['__invoke'],
            ['__debugInfo'],

            // Uppercase et al.
            ['__CONSTRUCT'],
            ['__Destruct'],
            ['__Call'],
            ['__callstatic'],
            ['__GET'],
            ['__SeT'],
            ['__isSet'],
            ['__unSet'],
            ['__SleeP'],
            ['__wakeUp'],
            ['__TOString'],
            ['__Set_State'],
            ['__CLONE'],
            ['__Invoke'],
            ['__Debuginfo'],
        ];

    }//end dataIsMagicMethodName()


    /**
     * Test non-magic method names.
     *
     * @param string $name The function name to test.
     *
     * @dataProvider dataIsMagicMethodNameFalse
     * @covers       \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isMagicMethodName
     *
     * @return void
     */
    public function testIsMagicMethodNameFalse($name)
    {
        $this->assertFalse(FunctionDeclarations::isMagicMethodName($name));

    }//end testIsMagicMethodNameFalse()


    /**
     * Test non-magic method names.
     *
     * @param string $name The function name to test.
     *
     * @dataProvider dataIsMagicMethodNameFalse
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
     * @see testIsMagicMethodNameFalse()
     *
     * @return array
     */
    public function dataIsMagicMethodNameFalse()
    {
        return [
            'no_underscore'         => ['construct'],
            'single_underscore'     => ['_destruct'],
            'triple_underscore'     => ['___call'],
            'not_magic_method_name' => ['__myFunction'],
        ];

    }//end dataIsMagicMethodNameFalse()


}//end class
