<?php
/**
 * Tests for the BaselineSet
 *
 * @author    Frank Dekker <fdekker@123inkt.nl>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Baseline;

use PHP_CodeSniffer\Baseline\BaselineSet;
use PHP_CodeSniffer\Baseline\ViolationBaseline;
use PHPUnit\Framework\TestCase;

/**
 * Test the logic of the baseline set
 *
 * @coversDefaultClass \PHP_CodeSniffer\Baseline\BaselineSet
 */
class BaselineSetTest extends TestCase
{


    /**
     * Test that contains find the correct sniff
     *
     * @covers ::addEntry
     * @covers ::contains
     * @return void
     */
    public function testSetContainsEntry()
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('sniff', 'foobar'));

        static::assertTrue($set->contains('sniff', 'foobar'));

    }//end testSetContainsEntry()


    /**
     * Test that contains differentiates between types
     *
     * @covers ::addEntry
     * @covers ::contains
     * @return void
     */
    public function testShouldFindEntryForIdenticalRules()
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('sniff', 'foo'));
        $set->addEntry(new ViolationBaseline('sniff', 'bar'));

        static::assertTrue($set->contains('sniff', 'foo'));
        static::assertTrue($set->contains('sniff', 'bar'));
        static::assertFalse($set->contains('sniff', 'unknown'));

    }//end testShouldFindEntryForIdenticalRules()


    /**
     * Test that unknown sniffs are not found
     *
     * @covers ::addEntry
     * @covers ::contains
     * @return void
     */
    public function testShouldNotFindEntryForNonExistingRule()
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('sniff', 'foo'));

        static::assertFalse($set->contains('unknown', 'foo'));

    }//end testShouldNotFindEntryForNonExistingRule()


}//end class
