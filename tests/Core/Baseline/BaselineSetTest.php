<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Common::isCamelCaps method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Baseline;

use PHP_CodeSniffer\Baseline\BaselineSet;
use PHP_CodeSniffer\Baseline\ViolationBaseline;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PHP_CodeSniffer\Baseline\BaselineSet
 */
class BaselineSetTest extends TestCase
{


    /**
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testSetContainsEntryWithoutMethodName()
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('sniff', 'foobar'));

        static::assertTrue($set->contains('sniff', 'foobar'));

    }//end testSetContainsEntryWithoutMethodName()


    /**
     * @covers ::addEntry
     * @covers ::contains
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
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testShouldNotFindEntryForNonExistingRule()
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('sniff', 'foo'));

        static::assertFalse($set->contains('unknown', 'foo'));

    }//end testShouldNotFindEntryForNonExistingRule()


}//end class
