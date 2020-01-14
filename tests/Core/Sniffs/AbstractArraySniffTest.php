<?php
/**
 * Tests for the \PHP_CodeSniffer\Sniffs\AbstractArraySniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Sniffs;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class AbstractArraySniffTest extends AbstractMethodUnitTest
{

    /**
     * The sniff objects we are testing.
     *
     * This extends the \PHP_CodeSniffer\Sniffs\AbstractArraySniff class to make the
     * internal workings of the sniff observable.
     *
     * @var \PHP_CodeSniffer\Sniffs\AbstractArraySniffTestable
     */
    protected static $sniff;


    /**
     * Initialize & tokenize \PHP_CodeSniffer\Files\File with code from the test case file.
     *
     * The test case file for a unit test class has to be in the same directory
     * directory and use the same file name as the test class, using the .inc extension.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$sniff = new AbstractArraySniffTestable();
        parent::setUpBeforeClass();

    }//end setUpBeforeClass()


    /**
     * Test an array of simple values only.
     *
     * @return void
     */
    public function testSimpleValues()
    {
        $token = $this->getTargetToken('/* testSimpleValues */', T_OPEN_SHORT_ARRAY);
        self::$sniff->process(self::$phpcsFile, $token);

        $expected = [
            0 => ['value_start' => ($token + 1)],
            1 => ['value_start' => ($token + 3)],
            2 => ['value_start' => ($token + 5)],
        ];

        $this->assertSame($expected, self::$sniff->indicies);

    }//end testSimpleValues()


    /**
     * Test an array of simple keys and values.
     *
     * @return void
     */
    public function testSimpleKeyValues()
    {
        $token = $this->getTargetToken('/* testSimpleKeyValues */', T_OPEN_SHORT_ARRAY);
        self::$sniff->process(self::$phpcsFile, $token);

        $expected = [
            0 => [
                'index_start' => ($token + 1),
                'index_end'   => ($token + 1),
                'arrow'       => ($token + 2),
                'value_start' => ($token + 3),
            ],
            1 => [
                'index_start' => ($token + 5),
                'index_end'   => ($token + 5),
                'arrow'       => ($token + 6),
                'value_start' => ($token + 7),
            ],
            2 => [
                'index_start' => ($token + 9),
                'index_end'   => ($token + 9),
                'arrow'       => ($token + 10),
                'value_start' => ($token + 11),
            ],
        ];

        $this->assertSame($expected, self::$sniff->indicies);

    }//end testSimpleKeyValues()


    /**
     * Test an array of simple keys and values.
     *
     * @return void
     */
    public function testMissingKeys()
    {
        $token = $this->getTargetToken('/* testMissingKeys */', T_OPEN_SHORT_ARRAY);
        self::$sniff->process(self::$phpcsFile, $token);

        $expected = [
            0 => [
                'index_start' => ($token + 1),
                'index_end'   => ($token + 1),
                'arrow'       => ($token + 2),
                'value_start' => ($token + 3),
            ],
            1 => [
                'value_start' => ($token + 5),
            ],
            2 => [
                'index_start' => ($token + 7),
                'index_end'   => ($token + 7),
                'arrow'       => ($token + 8),
                'value_start' => ($token + 9),
            ],
        ];

        $this->assertSame($expected, self::$sniff->indicies);

    }//end testMissingKeys()


    /**
     * Test an array with keys that span multiple tokens.
     *
     * @return void
     */
    public function testMultiTokenKeys()
    {
        $token = $this->getTargetToken('/* testMultiTokenKeys */', T_ARRAY);
        self::$sniff->process(self::$phpcsFile, $token);

        $expected = [
            0 => [
                'index_start' => ($token + 4),
                'index_end'   => ($token + 8),
                'arrow'       => ($token + 10),
                'value_start' => ($token + 12),
            ],
            1 => [
                'index_start' => ($token + 16),
                'index_end'   => ($token + 20),
                'arrow'       => ($token + 22),
                'value_start' => ($token + 24),
            ],
        ];

        $this->assertSame($expected, self::$sniff->indicies);

    }//end testMultiTokenKeys()


    /**
     * Test an array of simple keys and values.
     *
     * @return void
     */
    public function testMissingKeysCoalesceTernary()
    {
        $token = $this->getTargetToken('/* testMissingKeysCoalesceTernary */', T_OPEN_SHORT_ARRAY);
        self::$sniff->process(self::$phpcsFile, $token);

        $expected = [
            0 => [
                'index_start' => ($token + 3),
                'index_end'   => ($token + 3),
                'arrow'       => ($token + 5),
                'value_start' => ($token + 7),
            ],
            1 => [
                'value_start' => ($token + 31),
            ],
            2 => [
                'value_start' => ($token + 39),
            ],
        ];

        $this->assertSame($expected, self::$sniff->indicies);

    }//end testMissingKeysCoalesceTernary()


    /**
     * Test an array of ternary values.
     *
     * @return void
     */
    public function testTernaryValues()
    {
        $token = $this->getTargetToken('/* testTernaryValues */', T_OPEN_SHORT_ARRAY);
        self::$sniff->process(self::$phpcsFile, $token);

        $expected = [
            0 => [
                'index_start' => ($token + 3),
                'index_end'   => ($token + 3),
                'arrow'       => ($token + 5),
                'value_start' => ($token + 7),
            ],
            1 => [
                'index_start' => ($token + 32),
                'index_end'   => ($token + 32),
                'arrow'       => ($token + 34),
                'value_start' => ($token + 36),
            ],
            2 => [
                'index_start' => ($token + 72),
                'index_end'   => ($token + 72),
                'arrow'       => ($token + 74),
                'value_start' => ($token + 76),
            ],
        ];

        $this->assertSame($expected, self::$sniff->indicies);

    }//end testTernaryValues()


    /**
     * Test an array of heredocs.
     *
     * @return void
     */
    public function testHeredocValues()
    {
        $token = $this->getTargetToken('/* testHeredocValues */', T_ARRAY);
        self::$sniff->process(self::$phpcsFile, $token);

        $expected = [
            0 => [
                'value_start' => ($token + 4),
            ],
            1 => [
                'value_start' => ($token + 10),
            ],
        ];

        $this->assertSame($expected, self::$sniff->indicies);

    }//end testHeredocValues()


    /**
     * Test an array of with an arrow function as a value.
     *
     * @return void
     */
    public function testArrowFunctionValue()
    {
        $token = $this->getTargetToken('/* testArrowFunctionValue */', T_ARRAY);
        self::$sniff->process(self::$phpcsFile, $token);

        $expected = [
            0 => [
                'index_start' => ($token + 4),
                'index_end'   => ($token + 4),
                'arrow'       => ($token + 6),
                'value_start' => ($token + 8),
            ],
            1 => [
                'index_start' => ($token + 12),
                'index_end'   => ($token + 12),
                'arrow'       => ($token + 14),
                'value_start' => ($token + 16),
            ],
            2 => [
                'index_start' => ($token + 34),
                'index_end'   => ($token + 34),
                'arrow'       => ($token + 36),
                'value_start' => ($token + 38),
            ],
        ];

        $this->assertSame($expected, self::$sniff->indicies);

    }//end testArrowFunctionValue()


}//end class
