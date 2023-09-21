<?php
/**
 * Tests for the IgnoreList class.
 *
 * @author    Brad Jorsch <brad.jorsch@automattic.com>
 * @copyright 2023 Brad Jorsch
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core;

use PHP_CodeSniffer\Util\IgnoreList;
use PHPUnit\Framework\TestCase;

class IgnoreListTest extends TestCase
{


    /**
     * Test ignoringNone() works.
     *
     * @covers PHP_CodeSniffer\Util\IgnoreList::ignoringNone
     * @return void
     */
    public function testIgnoringNoneWorks()
    {
        $ignoreList = IgnoreList::ignoringNone();
        $this->assertInstanceOf(IgnoreList::class, $ignoreList);
        $this->assertFalse($ignoreList->check('Anything'));

    }//end testIgnoringNoneWorks()


    /**
     * Test ignoringAll() works.
     *
     * @covers PHP_CodeSniffer\Util\IgnoreList::ignoringAll
     * @return void
     */
    public function testIgnoringAllWorks()
    {
        $ignoreList = IgnoreList::ignoringAll();
        $this->assertInstanceOf(IgnoreList::class, $ignoreList);
        $this->assertTrue($ignoreList->check('Anything'));

    }//end testIgnoringAllWorks()


    /**
     * Test isEmpty() and isAll().
     *
     * @param IgnoreList $ignoreList  IgnoreList to test.
     * @param bool       $expectEmpty Expected return value from isEmpty().
     * @param bool       $expectAll   Expected return value from isAll().
     *
     * @return void
     *
     * @dataProvider dataIsEmptyAndAll
     * @covers       PHP_CodeSniffer\Util\IgnoreList::isEmpty
     * @covers       PHP_CodeSniffer\Util\IgnoreList::isAll
     */
    public function testIsEmptyAndAll($ignoreList, $expectEmpty, $expectAll)
    {
        $this->assertSame($expectEmpty, $ignoreList->isEmpty());
        $this->assertSame($expectAll, $ignoreList->isAll());

    }//end testIsEmptyAndAll()


    /**
     * Data provider.
     *
     * @see testIsEmptyAndAll()
     *
     * @return array
     */
    public function dataIsEmptyAndAll()
    {
        return [
            'fresh list'                                                    => [
                new IgnoreList(),
                true,
                false,
            ],
            'list from ignoringNone'                                        => [
                IgnoreList::ignoringNone(),
                true,
                false,
            ],
            'list from ignoringAll'                                         => [
                IgnoreList::ignoringAll(),
                false,
                true,
            ],
            'list from ignoringNone, something set to false'                => [
                IgnoreList::ignoringNone()->set('Foo.Bar', false),
                true,
                false,
            ],
            'list from ignoringNone, something set to true'                 => [
                IgnoreList::ignoringNone()->set('Foo.Bar', true),
                false,
                false,
            ],
            'list from ignoringAll, something set to false'                 => [
                IgnoreList::ignoringAll()->set('Foo.Bar', false),
                false,
                false,
            ],
            'list from ignoringAll, something set to true'                  => [
                IgnoreList::ignoringAll()->set('Foo.Bar', true),
                false,
                true,
            ],
            'list from ignoringNone, something set to true then overridden' => [
                IgnoreList::ignoringNone()->set('Foo.Bar', true)->set('Foo', false),
                true,
                false,
            ],
            'list from ignoringAll, something set to false then overridden' => [
                IgnoreList::ignoringAll()->set('Foo.Bar', false)->set('Foo', true),
                false,
                true,
            ],
        ];

    }//end dataIsEmptyAndAll()


    /**
     * Test check() and set().
     *
     * @param array $toSet   Associative array of $code => $ignore to pass to set().
     * @param array $toCheck Associative array of $code => $expect to pass to check().
     *
     * @return void
     *
     * @dataProvider dataCheckAndSet
     * @covers       PHP_CodeSniffer\Util\IgnoreList::check
     * @covers       PHP_CodeSniffer\Util\IgnoreList::set
     */
    public function testCheckAndSet($toSet, $toCheck)
    {
        $ignoreList = new IgnoreList();
        foreach ($toSet as $code => $ignore) {
            $this->assertSame($ignoreList, $ignoreList->set($code, $ignore));
        }

        foreach ($toCheck as $code => $expect) {
            $this->assertSame($expect, $ignoreList->check($code));
        }

    }//end testCheckAndSet()


    /**
     * Data provider.
     *
     * @see testCheckAndSet()
     *
     * @return array
     */
    public function dataCheckAndSet()
    {
        return [
            'set a code'                                                                       => [
                ['Standard.Category.Sniff.Code' => true],
                [
                    'Standard.Category.Sniff.Code'      => true,
                    'Standard.Category.Sniff.OtherCode' => false,
                    'Standard.Category.OtherSniff.Code' => false,
                    'Standard.OtherCategory.Sniff.Code' => false,
                    'OtherStandard.Category.Sniff.Code' => false,
                ],
            ],
            'set a sniff'                                                                      => [
                ['Standard.Category.Sniff' => true],
                [
                    'Standard.Category.Sniff.Code'      => true,
                    'Standard.Category.Sniff.OtherCode' => true,
                    'Standard.Category.OtherSniff.Code' => false,
                    'Standard.OtherCategory.Sniff.Code' => false,
                    'OtherStandard.Category.Sniff.Code' => false,
                ],
            ],
            'set a category'                                                                   => [
                ['Standard.Category' => true],
                [
                    'Standard.Category.Sniff.Code'      => true,
                    'Standard.Category.Sniff.OtherCode' => true,
                    'Standard.Category.OtherSniff.Code' => true,
                    'Standard.OtherCategory.Sniff.Code' => false,
                    'OtherStandard.Category.Sniff.Code' => false,
                ],
            ],
            'set a standard'                                                                   => [
                ['Standard' => true],
                [
                    'Standard.Category.Sniff.Code'      => true,
                    'Standard.Category.Sniff.OtherCode' => true,
                    'Standard.Category.OtherSniff.Code' => true,
                    'Standard.OtherCategory.Sniff.Code' => true,
                    'OtherStandard.Category.Sniff.Code' => false,
                ],
            ],
            'set a standard, unignore a sniff in it'                                           => [
                [
                    'Standard'                => true,
                    'Standard.Category.Sniff' => false,
                ],
                [
                    'Standard.Category.Sniff.Code'      => false,
                    'Standard.Category.Sniff.OtherCode' => false,
                    'Standard.Category.OtherSniff.Code' => true,
                    'Standard.OtherCategory.Sniff.Code' => true,
                    'OtherStandard.Category.Sniff.Code' => false,
                ],
            ],
            'set a standard, unignore a category in it, ignore a sniff in that'                => [
                [
                    'Standard'                => true,
                    'Standard.Category'       => false,
                    'Standard.Category.Sniff' => true,
                ],
                [
                    'Standard.Category.Sniff.Code'      => true,
                    'Standard.Category.Sniff.OtherCode' => true,
                    'Standard.Category.OtherSniff.Code' => false,
                    'Standard.OtherCategory.Sniff.Code' => true,
                    'OtherStandard.Category.Sniff.Code' => false,
                ],
            ],
            'ignore some sniffs, then override some of those by unignoring the whole category' => [
                [
                    'Standard.Category1.Sniff1' => true,
                    'Standard.Category1.Sniff2' => true,
                    'Standard.Category2.Sniff1' => true,
                    'Standard.Category2.Sniff2' => true,
                    'Standard.Category1'        => false,
                ],
                [
                    'Standard.Category1.Sniff1' => false,
                    'Standard.Category1.Sniff2' => false,
                    'Standard.Category2.Sniff1' => true,
                    'Standard.Category2.Sniff2' => true,
                ],
            ],
        ];

    }//end dataCheckAndSet()


}//end class
