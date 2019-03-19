<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Orthography::isLastCharPunctuation() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Orthography;

use PHPUnit\Framework\TestCase;
use PHP_CodeSniffer\Util\Sniffs\Orthography;

class IsLastCharPunctuationTest extends TestCase
{


    /**
     * Test correctly detecting sentence end punctuation.
     *
     * @param string $input        The input string.
     * @param bool   $expected     The expected function output.
     * @param string $allowedChars Optional. Custom punctuation character set.
     *
     * @dataProvider dataIsLastCharPunctuation
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Orthography::isLastCharPunctuation
     *
     * @return void
     */
    public function testIsLastCharPunctuation($input, $expected, $allowedChars=null)
    {
        if (isset($allowedChars) === true) {
            $result = Orthography::isLastCharPunctuation($input, $allowedChars);
        } else {
            $result = Orthography::isLastCharPunctuation($input);
        }

        $this->assertSame($expected, $result);

    }//end testIsLastCharPunctuation()


    /**
     * Data provider.
     *
     * @see testIsLastCharPunctuation()
     *
     * @return array
     */
    public function dataIsLastCharPunctuation()
    {
        return [
            // Quotes should be stripped before passing the string.
            'double-quoted'                          => [
                '"This is a test."',
                false,
            ],
            'single-quoted'                          => [
                "'This is a test?'",
                false,
            ],

            // Invalid end char.
            'no-punctuation'                         => [
                'This is a test',
                false,
            ],
            'invalid-punctuation'                    => [
                'This is a test;',
                false,
            ],
            'invalid-punctuationtrailing-whitespace' => [
                'This is a test;       ',
                false,
            ],

            // Valid end char, default charset.
            'valid'                                  => [
                'This is a test.',
                true,
            ],
            'valid-trailing-whitespace'              => [
                'This is a test.
',
                true,
            ],

            // Invalid end char, custom charset.
            'invalid-custom'                         => [
                'This is a test.',
                false,
                '!?,;#',
            ],

            // Valid end char, custom charset.
            'valid-custom-1'                         => [
                'This is a test;',
                true,
                '!?,;#',
            ],
            'valid-custom-2'                         => [
                'This is a test!',
                true,
                '!?,;#',
            ],
            'valid-custom-3'                         => [
                'Is this is a test?',
                true,
                '!?,;#',
            ],
            'valid-custom-4'                         => [
                'This is a test,',
                true,
                '!?,;#',
            ],
            'valid-custom-5'                         => [
                'This is a test#',
                true,
                '!?,;#',
            ],
        ];

    }//end dataIsLastCharPunctuation()


}//end class
