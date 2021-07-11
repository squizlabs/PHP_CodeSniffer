<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Common::isCamelCaps method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Baseline;

use PHP_CodeSniffer\Baseline\ViolationBaseline;
use PHPUnit\Framework\TestCase;

/**
 * Test the violation baseline data model
 *
 * @coversDefaultClass \PHP_CodeSniffer\Baseline\ViolationBaseline
 */
class ViolationBaselineTest extends TestCase
{


    /**
     * Test the sniff name is returned
     *
     * @covers ::__construct
     * @covers ::getSniffName
     * @return void
     */
    public function testGetSniffName()
    {
        $violation = new ViolationBaseline('sniff', 'foobar');
        static::assertSame('sniff', $violation->getSniffName());

    }//end testGetSniffName()


    /**
     * Test the give file matches the baseline correctly
     *
     * @covers ::__construct
     * @covers ::matches
     * @return void
     */
    public function testMatches()
    {
        $violation = new ViolationBaseline('sniff', 'foobar.txt');
        static::assertTrue($violation->matches('foobar.txt'));
        static::assertTrue($violation->matches('/test/foobar.txt'));
        static::assertFalse($violation->matches('foo.txt'));

    }//end testMatches()


}//end class
