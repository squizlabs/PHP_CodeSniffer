<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\TextStrings::stripQuotes() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2016-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\TextStrings;

use PHPUnit\Framework\TestCase;
use PHP_CodeSniffer\Util\Sniffs\TextStrings;

class StripQuotesTest extends TestCase
{


    /**
     * Test correctly stripping quotes surrounding text strings.
     *
     * @param string $input    The input string.
     * @param string $expected The expected function output.
     *
     * @dataProvider dataStripQuotes
     * @covers       \PHP_CodeSniffer\Util\Sniffs\TextStrings::stripQuotes
     *
     * @return void
     */
    public function testStripQuotes($input, $expected)
    {
        $this->assertSame($expected, TextStrings::stripQuotes($input));

    }//end testStripQuotes()


    /**
     * Data provider.
     *
     * @see testStripQuotes()
     *
     * @return array
     */
    public function dataStripQuotes()
    {
        return [
            [
                '"dir_name"',
                'dir_name',
            ],
            [
                "'soap.wsdl_cache'",
                "soap.wsdl_cache",
            ],
            [
                '"arbitrary-\'string\" with\' quotes within"',
                'arbitrary-\'string\" with\' quotes within',
            ],
            [
                '"\'quoted_name\'"',
                '\'quoted_name\'',
            ],
            [
                "'\"quoted\" start of string'",
                '"quoted" start of string',
            ],
            [
                "'no stripping when there is only a start quote",
                "'no stripping when there is only a start quote",
            ],
            [
                'no stripping when there is only an end quote"',
                'no stripping when there is only an end quote"',
            ],
            [
                "'no stripping when quotes at start/end are mismatched\"",
                "'no stripping when quotes at start/end are mismatched\"",
            ],
        ];

    }//end dataStripQuotes()


}//end class
