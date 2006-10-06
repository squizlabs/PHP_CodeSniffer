<?php
/**
 * +------------------------------------------------------------------------+
 * | BSD Licence                                                            |
 * +------------------------------------------------------------------------+
 * | This software is available to you under the BSD license,               |
 * | available in the LICENSE file accompanying this software.              |
 * | You may obtain a copy of the License at                                |
 * |                                                                        |
 * | http://matrix.squiz.net/developer/tools/php_cs/licence                 |
 * |                                                                        |
 * | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS    |
 * | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT      |
 * | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR  |
 * | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT   |
 * | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,  |
 * | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT       |
 * | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,  |
 * | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY  |
 * | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT    |
 * | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE  |
 * | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.   |
 * +------------------------------------------------------------------------+
 * | Copyright (c), 2006 Squiz Pty Ltd (ABN 77 084 670 600).                |
 * | All rights reserved.                                                   |
 * +------------------------------------------------------------------------+
 *
 * @package  PHP_CodeSniffer
 * @category Testing
 * @author   Squiz Pty Ltd
 */

require_once 'PHPUnit2/Framework/TestCase.php';
require_once 'PHP/CodeSniffer.php';

/**
 * Tests for the PHP_CodeSniffer:isCamelCaps method.
 *
 * @package  PHP_CodeSniffer
 * @category Testing
 * @author   Squiz Pty Ltd
 */
class Core_IsCamelCapsTest extends PHPUnit2_Framework_TestCase
{


    /**
     * Test valid public function/method names.
     *
     * @return void
     */
    public function testValidNotClassFormatPublic()
    {
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('thisIsCamelCaps', false, true, true));
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('thisISCamelCaps', false, true, false));

    }//end testValidNotClassFormatPublic()


    /**
     * Test invalid public function/method names.
     *
     * @return void
     */
    public function testInvalidNotClassFormatPublic()
    {
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('_thisIsCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('thisISCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('ThisIsCamelCaps', false, true, true));

        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('3thisIsCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('*thisIsCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('-thisIsCamelCaps', false, true, true));

        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('this*IsCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('this-IsCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('this_IsCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('this_is_camel_caps', false, true, true));

    }//end testInvalidNotClassFormatPublic()


    /**
     * Test valid private method names.
     *
     * @return void
     */
    public function testValidNotClassFormatPrivate()
    {
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('_thisIsCamelCaps', false, false, true));
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('_thisISCamelCaps', false, false, false));

    }//end testValidNotClassFormatPrivate()


    /**
     * Test invalid private method names.
     *
     * @return void
     */
    public function testInvalidNotClassFormatPrivate()
    {
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('thisIsCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('_thisISCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('_ThisIsCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('__thisIsCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('__thisISCamelCaps', false, false, false));

        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('3thisIsCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('*thisIsCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('-thisIsCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('_this_is_camel_caps', false, false, true));

    }//end testInvalidNotClassFormatPrivate()


    /**
     * Test valid class names.
     *
     * @return void
     */
    public function testValidClassFormatPublic()
    {
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('ThisIsCamelCaps', true, true, true));
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('ThisISCamelCaps', true, true, false));

    }//end testValidClassFormatPublic()


    /**
     * Test invalid class names.
     *
     * @return void
     */
    public function testInvalidClassFormat()
    {
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('thisIsCamelCaps', true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('This3IsCamelCaps', true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('This-IsCamelCaps', true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('This_Is_Camel_Caps', true));

    }//end testInvalidClassFormat()


    /**
     * Test invalid class names with the private flag set.
     *
     * Note that the private flag is ignored if the class format
     * flag is set, so these names are all invalid.
     *
     * @return void
     */
    public function testInvalidClassFormatPrivate()
    {
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('_ThisIsCamelCaps', true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('_ThisIsCamelCaps', true, false));

    }//end testInvalidClassFormatPrivate()


}//end class

?>
