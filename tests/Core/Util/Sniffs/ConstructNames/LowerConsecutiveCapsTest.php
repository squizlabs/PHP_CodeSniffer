<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\ConstructNames::lowerConsecutiveCaps() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\ConstructNames;

use PHP_CodeSniffer\Util\Sniffs\ConstructNames;
use PHPUnit\Framework\TestCase;

class LowerConsecutiveCapsTest extends TestCase
{


    /**
     * Test lowering consecutive caps in a text string.
     *
     * @param string $string   The string.
     * @param string $expected The expected function return value.
     *
     * @dataProvider dataLowerConsecutiveCaps
     * @covers       \PHP_CodeSniffer\Util\Sniffs\ConstructNames::lowerConsecutiveCaps
     *
     * @return void
     */
    public function testLowerConsecutiveCaps($string, $expected)
    {
        $this->assertSame($expected, ConstructNames::lowerConsecutiveCaps($string));

    }//end testLowerConsecutiveCaps()


    /**
     * Data provider.
     *
     * @see testLowerConsecutiveCaps()
     *
     * @return array
     */
    public function dataLowerConsecutiveCaps()
    {
        $data = [
            // Deliberately empty.
            [
                '',
                '',
            ],
            [
                'nocaps',
                'nocaps',
            ],
            [
                'noConsecutiveCaps',
                'noConsecutiveCaps',
            ],
            [
                'IsAMethod',
                'IsAmethod',
            ],
            [
                'IsThisAI',
                'IsThisAi',
            ],
            [
                'IsThisAI20',
                'IsThisAi20',
            ],
            [
                'Is_A_Method',
                'Is_A_Method',
            ],
            [
                'PHPMethod',
                'PhpMethod',
            ],
            [
                'PHP7Method',
                'Php7Method',
            ],
            [
                'MyPHPMethod',
                'MyPhpMethod',
            ],
            [
                'My_PHP_Method',
                'My_Php_Method',
            ],
            [
                'MyMethodInPHP',
                'MyMethodInPhp',
            ],
            [
                'MyMethodInPHP7',
                'MyMethodInPhp7',
            ],
            [
                'My-CSS-Selector',
                'My-Css-Selector',
            ],
            [
                'SomeCAPSAndMoreCAPSAndMORE',
                'SomeCapsAndMoreCapsAndMore',
            ],
        ];

        $unicodeData = [
            // ASCII Extended.
            [
                'IÑTËRNÂTÎÔNÀLÍŽÆTIØN',
                'Iñtërnâtîônàlížætiøn',
            ],

            // Russian, no consecutive caps.
            [
                'МояРабота',
                'МояРабота',
            ],

            // Russian, consecutive caps.
            [
                'МОЯРабота',
                'МояРабота',
            ],

            // Russian, consecutive caps with separator.
            [
                'МОЯ_Работа',
                'Моя_Работа',
            ],
        ];

        // Add Unicode name testcases.
        if (function_exists('mb_strtolower') === true) {
            $data = array_merge($data, $unicodeData);
        } else {
            // If MBString is not available, non-ASCII input should be returned unchanged.
            foreach ($unicodeData as $dataset) {
                $data[] = [
                    $dataset[0],
                    $dataset[0],
                ];
            }
        }

        return $data;

    }//end dataLowerConsecutiveCaps()


}//end class
