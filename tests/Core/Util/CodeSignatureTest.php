<?php
/**
 * Tests the generation of code signature based on tokens
 *
 * @author    Frank Dekker <fdekker@123inkt.nl>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util;

use PHP_CodeSniffer\Util\CodeSignature;
use PHPUnit\Framework\TestCase;

/**
 * Tests the generation of code signature based on tokens
 *
 * @coversDefaultClass \PHP_CodeSniffer\Util\CodeSignature
 */
class CodeSignatureTest extends TestCase
{


    /**
     * Test the code signature hash generation
     *
     * @param int    $lineNr   the line nr within the file
     * @param string $expected the expected signature
     *
     * @return void
     *
     * @covers       ::createSignature
     * @dataProvider dataProvider
     */
    public function testCreateSignature($lineNr, $expected)
    {
        $tokens = [
            [
                'content' => 'line1',
                'line'    => 1,
            ],
            [
                'content' => 'line2',
                'line'    => 2,
            ],
            [
                'content' => 'line3',
                'line'    => 3,
            ],
            [
                'content' => "\r\n",
                'line'    => 3,
            ],
            [
                'content' => 'line4',
                'line'    => 4,
            ],
            [
                'content' => 'line5',
                'line'    => 5,
            ],
        ];

        $signature = CodeSignature::createSignature($tokens, $lineNr);
        static::assertSame($expected, $signature);

    }//end testCreateSignature()


    /**
     * Provide edge case scenario's for the code signature
     *
     * @return array<string, array<int, string>>
     */
    public function dataProvider()
    {
        return [
            'first line of file'  => [
                1,
                hash('sha1', 'line1line2'),
            ],
            'middle line of file' => [
                3,
                hash('sha1', 'line2line3line4'),
            ],
            'last line of file'   => [
                5,
                hash('sha1', 'line4line5'),
            ],
        ];

    }//end dataProvider()


}//end class
