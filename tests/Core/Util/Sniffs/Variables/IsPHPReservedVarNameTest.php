<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Variables::isPHPReservedVarName() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Variables;

use PHP_CodeSniffer\Util\Sniffs\Variables;
use PHPUnit\Framework\TestCase;

class IsPHPReservedVarNameTest extends TestCase
{


    /**
     * Test valid PHP reserved variable names.
     *
     * @param string $name The variable name to test.
     *
     * @dataProvider dataIsPHPReservedVarName
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Variables::isPHPReservedVarName
     *
     * @return void
     */
    public function testIsPHPReservedVarName($name)
    {
        $this->assertTrue(Variables::isPHPReservedVarName($name));

    }//end testIsPHPReservedVarName()


    /**
     * Data provider.
     *
     * @see testIsPHPReservedVarName()
     *
     * @return array
     */
    public function dataIsPHPReservedVarName()
    {
        return [
            // With dollar sign.
            ['$_SERVER'],
            ['$_GET'],
            ['$_POST'],
            ['$_REQUEST'],
            ['$_SESSION'],
            ['$_ENV'],
            ['$_COOKIE'],
            ['$_FILES'],
            ['$GLOBALS'],
            ['$http_response_header'],
            ['$argc'],
            ['$argv'],
            ['$HTTP_RAW_POST_DATA'],
            ['$php_errormsg'],
            ['$HTTP_SERVER_VARS'],
            ['$HTTP_GET_VARS'],
            ['$HTTP_POST_VARS'],
            ['$HTTP_SESSION_VARS'],
            ['$HTTP_ENV_VARS'],
            ['$HTTP_COOKIE_VARS'],
            ['$HTTP_POST_FILES'],

            // Without dollar sign.
            ['_SERVER'],
            ['_GET'],
            ['_POST'],
            ['_REQUEST'],
            ['_SESSION'],
            ['_ENV'],
            ['_COOKIE'],
            ['_FILES'],
            ['GLOBALS'],
            ['http_response_header'],
            ['argc'],
            ['argv'],
            ['HTTP_RAW_POST_DATA'],
            ['php_errormsg'],
            ['HTTP_SERVER_VARS'],
            ['HTTP_GET_VARS'],
            ['HTTP_POST_VARS'],
            ['HTTP_SESSION_VARS'],
            ['HTTP_ENV_VARS'],
            ['HTTP_COOKIE_VARS'],
            ['HTTP_POST_FILES'],
        ];

    }//end dataIsPHPReservedVarName()


    /**
     * Test non-reserved variable names.
     *
     * @param string $name The variable name to test.
     *
     * @dataProvider dataIsPHPReservedVarNameFalse
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Variables::isPHPReservedVarName
     *
     * @return void
     */
    public function testIsPHPReservedVarNameFalse($name)
    {
        $this->assertFalse(Variables::isPHPReservedVarName($name));

    }//end testIsPHPReservedVarNameFalse()


    /**
     * Data provider.
     *
     * @see testIsPHPReservedVarNameFalse()
     *
     * @return array
     */
    public function dataIsPHPReservedVarNameFalse()
    {
        return [
            // Different case.
            ['$_Server'],
            ['$_get'],
            ['$_pOST'],
            ['$HTTP_RESPONSE_HEADER'],
            ['_EnV'],
            ['PHP_errormsg'],

            // Shouldn't be possible, but all the same: double dollar.
            ['$$_REQUEST'],

            // No underscore.
            ['$SERVER'],
            ['SERVER'],

            // Double underscore.
            ['$__SERVER'],
            ['__SERVER'],

            // Globals with underscore.
            ['$_GLOBALS'],
            ['_GLOBALS'],

            // Some completely different variable name.
            ['my_php_errormsg'],
        ];

    }//end dataIsPHPReservedVarNameFalse()


}//end class
