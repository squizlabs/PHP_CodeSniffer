<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getDoubleArrowPosition() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\PassedParameters;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\PassedParameters;

class GetDoubleArrowPositionTest extends AbstractMethodUnitTest
{


    /**
     * Cache for the parsed parameters array.
     *
     * @var array <string> => <int>
     */
    private $parameters = [];


    /**
     * Set up the parsed parameters cache for the tests.
     *
     * Retrieves the parsed parameters array only once and caches
     * it as it won't change between the tests anyway.
     *
     * @return void
     */
    protected function setUp()
    {
        if (empty($this->parameters) === true) {
            $target           = $this->getTargetToken('/* testGetDoubleArrowPosition */', [T_OPEN_SHORT_ARRAY]);
            $this->parameters = PassedParameters::getParameters(self::$phpcsFile, $target);
        }

    }//end setUp()


    /**
     * Test receiving an expected exception when an invalid start position is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage Invalid start and/or end position passed to getDoubleArrowPosition(). Received: $start -10, $end 10
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getDoubleArrowPosition
     *
     * @return void
     */
    public function testInvalidStartPositionException()
    {
        $result = PassedParameters::getDoubleArrowPosition(self::$phpcsFile, -10, 10);

    }//end testInvalidStartPositionException()


    /**
     * Test receiving an expected exception when an invalid end position is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage Invalid start and/or end position passed to getDoubleArrowPosition(). Received: $start 0, $end 100000
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getDoubleArrowPosition
     *
     * @return void
     */
    public function testInvalidEndPositionException()
    {
        $result = PassedParameters::getDoubleArrowPosition(self::$phpcsFile, 0, 100000);

    }//end testInvalidEndPositionException()


    /**
     * Test receiving an expected exception when the start position is after the end position.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage Invalid start and/or end position passed to getDoubleArrowPosition(). Received: $start 10, $end 5
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getDoubleArrowPosition
     *
     * @return void
     */
    public function testInvalidStartEndPositionException()
    {
        $result = PassedParameters::getDoubleArrowPosition(self::$phpcsFile, 10, 5);

    }//end testInvalidStartEndPositionException()


    /**
     * Test retrieving the position of the double arrow for an array parameter.
     *
     * @param string $testMarker The comment which is part of the target array item in the test file.
     * @param array  $expected   The expected function call result.
     *
     * @dataProvider dataGetDoubleArrowPosition
     * @covers       \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getDoubleArrowPosition
     *
     * @return void
     */
    public function testGetDoubleArrowPosition($testMarker, $expected)
    {
        foreach ($this->parameters as $index => $values) {
            if (strpos($values['raw'], $testMarker) !== false) {
                $start = $values['start'];
                $end   = $values['end'];
                break;
            }
        }

        if (isset($start, $end) === false) {
            $this->markTestIncomplete('Test case not found for '.$testMarker);
        }

        // Expected double arrow positions are set as offsets
        // in relation to the start of the array item.
        // Change these to exact positions.
        if ($expected !== false) {
            $expected = ($start + $expected);
        }

        $result = PassedParameters::getDoubleArrowPosition(self::$phpcsFile, $start, $end);
        $this->assertSame($expected, $result);

    }//end testGetDoubleArrowPosition()


    /**
     * Data provider.
     *
     * @see testGetDoubleArrowPosition()
     *
     * @return array
     */
    public function dataGetDoubleArrowPosition()
    {
        return [
            [
                '/* testValueNoArrow */',
                false,
            ],
            [
                '/* testArrowNumericIndex */',
                8,
            ],
            [
                '/* testArrowStringIndex */',
                8,
            ],
            [
                '/* testArrowMultiTokenIndex */',
                12,
            ],
            [
                '/* testNoArrowValueShortArray */',
                false,
            ],
            [
                '/* testNoArrowValueLongArray */',
                false,
            ],
            [
                '/* testNoArrowValueNestedArrays */',
                false,
            ],
            [
                '/* testNoArrowValueClosure */',
                false,
            ],
            [
                '/* testArrowValueShortArray */',
                8,
            ],
            [
                '/* testArrowValueLongArray */',
                8,
            ],
            [
                '/* testArrowValueClosure */',
                8,
            ],
        ];

    }//end dataGetDoubleArrowPosition()


}//end class
