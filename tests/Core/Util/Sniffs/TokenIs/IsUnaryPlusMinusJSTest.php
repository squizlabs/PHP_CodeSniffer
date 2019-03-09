<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\TokenIs::isUnaryPlusMinus() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\TokenIs;

class IsUnaryPlusMinusJSTest extends IsUnaryPlusMinusTest
{

    /**
     * The file extension of the test case file (without leading dot).
     *
     * @var string
     */
    protected static $fileExtension = 'js';


    /**
     * Data provider.
     *
     * @see IsUnaryPlusMinusTest::testIsUnaryPlusMinus()
     *
     * @return array
     */
    public function dataIsUnaryPlusMinus()
    {
        return [
            [
                '/* testNonUnaryPlus */',
                false,
            ],
            [
                '/* testNonUnaryMinus */',
                false,
            ],
            [
                '/* testUnaryMinusColon */',
                true,
            ],
            [
                '/* testUnaryMinusCase */',
                true,
            ],
            [
                '/* testUnaryMinusInlineIf */',
                true,
            ],
            [
                '/* testUnaryPlusInlineThen */',
                true,
            ],
            [
                '/* testUnaryMinusInlineLogical */',
                true,
            ],
        ];

    }//end dataIsUnaryPlusMinus()


}//end class
