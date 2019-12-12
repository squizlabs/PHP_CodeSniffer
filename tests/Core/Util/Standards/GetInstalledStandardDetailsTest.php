<?php
/**
 * Tests the adding of the "parenthesis" keys to an anonymous class token.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Standards;

use PHP_CodeSniffer\Util\Standards;
use PHPUnit\Framework\TestCase;

class GetInstalledStandardDetailsTest extends TestCase
{


    /**
     * Test hidden standards functionality.
     *
     * @return void
     */
    public function testGetInstalledStandardDetailsIncludeHiddenParam()
    {
        $installed = Standards::getInstalledStandardDetails();
        $this->assertFalse(array_key_exists('Generic', $installed));
        $this->assertTrue(array_key_exists('PSR12', $installed));

        $installed = Standards::getInstalledStandardDetails(true);
        $this->assertTrue(array_key_exists('Generic', $installed));
        $this->assertTrue(array_key_exists('PSR12', $installed));

    }//end testGetInstalledStandardDetailsIncludeHiddenParam()


}//end class
