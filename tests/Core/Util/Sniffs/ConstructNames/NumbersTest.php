<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\ConstructNames::hasNumbers(), the
 * \PHP_CodeSniffer\Util\Sniffs\ConstructNames::ltrimNumbers() and the
 * \PHP_CodeSniffer\Util\Sniffs\ConstructNames::removeNumbers() methods.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\ConstructNames;

use PHP_CodeSniffer\Util\Sniffs\ConstructNames;
use PHPUnit\Framework\TestCase;

class NumbersTest extends TestCase
{


    /**
     * Test verifying whether a text string contains numeric characters.
     *
     * @param string $string   Input string.
     * @param array  $expected Expected function output for the various functions.
     *
     * @dataProvider dataStringsWithNumbers
     * @covers       \PHP_CodeSniffer\Util\Sniffs\ConstructNames::hasNumbers
     *
     * @return void
     */
    public function testHasNumbers($string, $expected)
    {
        $this->assertSame($expected['has'], ConstructNames::hasNumbers($string));

    }//end testHasNumbers()


    /**
     * Test trimming numbers from the beginning of a text string.
     *
     * @param string $string   Input string.
     * @param array  $expected Expected function output for the various functions.
     *
     * @dataProvider dataStringsWithNumbers
     * @covers       \PHP_CodeSniffer\Util\Sniffs\ConstructNames::ltrimNumbers
     *
     * @return void
     */
    public function testLtrimNumbers($string, $expected)
    {
        $this->assertSame($expected['ltrim'], ConstructNames::ltrimNumbers($string));

    }//end testLtrimNumbers()


    /**
     * Test removing all numbers from a text string.
     *
     * @param string $string   Input string.
     * @param array  $expected Expected function output for the various functions.
     *
     * @dataProvider dataStringsWithNumbers
     * @covers       \PHP_CodeSniffer\Util\Sniffs\ConstructNames::removeNumbers
     *
     * @return void
     */
    public function testRemoveNumbers($string, $expected)
    {
        $this->assertSame($expected['remove'], ConstructNames::removeNumbers($string));

    }//end testRemoveNumbers()


    /**
     * Data provider.
     *
     * @see testHasNumbers()
     * @see testLtrimNumbers()
     * @see testRemoveNumbers()
     *
     * @return array
     */
    public function dataStringsWithNumbers()
    {
        return [
            [
                // Deliberately empty.
                '',
                [
                    'has'    => false,
                    'ltrim'  => '',
                    'remove' => '',
                ],
            ],
            [
                'NoNumbers',
                [
                    'has'    => false,
                    'ltrim'  => 'NoNumbers',
                    'remove' => 'NoNumbers',
                ],
            ],
            [
                '1',
                [
                    'has'    => true,
                    'ltrim'  => '',
                    'remove' => '',
                ],
            ],
            [
                '1234567890',
                [
                    'has'    => true,
                    'ltrim'  => '',
                    'remove' => '',
                ],
            ],
            [
                '1Pancake',
                [
                    'has'    => true,
                    'ltrim'  => 'Pancake',
                    'remove' => 'Pancake',
                ],
            ],
            [
                '123Pancakes',
                [
                    'has'    => true,
                    'ltrim'  => 'Pancakes',
                    'remove' => 'Pancakes',
                ],
            ],
            [
                '1Pancake2Pancakes',
                [
                    'has'    => true,
                    'ltrim'  => 'Pancake2Pancakes',
                    'remove' => 'PancakePancakes',
                ],
            ],
            [
                '123Pancake456Pancakes789Pancakes',
                [
                    'has'    => true,
                    'ltrim'  => 'Pancake456Pancakes789Pancakes',
                    'remove' => 'PancakePancakesPancakes',
                ],
            ],
            [
                '½Pancake⅝Pancake',
                [
                    'has'    => true,
                    'ltrim'  => 'Pancake⅝Pancake',
                    'remove' => 'PancakePancake',
                ],
            ],
            [
                'ⅦPancakesⅲPancakes',
                [
                    'has'    => true,
                    'ltrim'  => 'PancakesⅲPancakes',
                    'remove' => 'PancakesPancakes',
                ],
            ],
            [
                '๑٦⑱Pancake٨๔⓳Pancakes㊱௫Pancakes',
                [
                    'has'    => true,
                    'ltrim'  => 'Pancake٨๔⓳Pancakes㊱௫Pancakes',
                    'remove' => 'PancakePancakesPancakes',
                ],
            ],
        ];

    }//end dataStringsWithNumbers()


}//end class
