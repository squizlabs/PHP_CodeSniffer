<?php
/**
 * Tests for the ViolationBaseline
 *
 * @author    Frank Dekker <fdekker@123inkt.nl>
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
        $violation = new ViolationBaseline('sniff', 'foobar', 'signature');
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
        $violation = new ViolationBaseline('sniff', 'foobar.txt', 'signature');
        static::assertTrue($violation->matches('foobar.txt', 'signature'));
        static::assertTrue($violation->matches('/test/foobar.txt', 'signature'));
        static::assertFalse($violation->matches('foo.txt', 'signature'));
        static::assertFalse($violation->matches('foobar.txt', 'bad-signature'));

    }//end testMatches()


}//end class
