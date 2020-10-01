<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Common::isCamelCaps method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Autoloader;

use PHPUnit\Framework\TestCase;

class DetermineLoadedClassTest extends TestCase
{


    /**
     * Load the test files.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        include __DIR__.'/TestFiles/Sub/C.inc';

    }//end setUpBeforeClass()


    /**
     * Test for when class list is ordered.
     *
     * @return void
     */
    public function testOrdered()
    {
        $classesBeforeLoad = [
            'classes'    => [],
            'interfaces' => [],
            'traits'     => [],
        ];

        $classesAfterLoad = [
            'classes'    => [
                'PHP_CodeSniffer\Tests\Core\Autoloader\A',
                'PHP_CodeSniffer\Tests\Core\Autoloader\B',
                'PHP_CodeSniffer\Tests\Core\Autoloader\C',
                'PHP_CodeSniffer\Tests\Core\Autoloader\Sub\C',
            ],
            'interfaces' => [],
            'traits'     => [],
        ];

        $className = \PHP_CodeSniffer\Autoload::determineLoadedClass($classesBeforeLoad, $classesAfterLoad);
        $this->assertEquals('PHP_CodeSniffer\Tests\Core\Autoloader\Sub\C', $className);

    }//end testOrdered()


    /**
     * Test for when class list is out of order.
     *
     * @return void
     */
    public function testUnordered()
    {
        $classesBeforeLoad = [
            'classes'    => [],
            'interfaces' => [],
            'traits'     => [],
        ];

        $classesAfterLoad = [
            'classes'    => [
                'PHP_CodeSniffer\Tests\Core\Autoloader\A',
                'PHP_CodeSniffer\Tests\Core\Autoloader\Sub\C',
                'PHP_CodeSniffer\Tests\Core\Autoloader\C',
                'PHP_CodeSniffer\Tests\Core\Autoloader\B',
            ],
            'interfaces' => [],
            'traits'     => [],
        ];

        $className = \PHP_CodeSniffer\Autoload::determineLoadedClass($classesBeforeLoad, $classesAfterLoad);
        $this->assertEquals('PHP_CodeSniffer\Tests\Core\Autoloader\Sub\C', $className);

        $classesAfterLoad = [
            'classes'    => [
                'PHP_CodeSniffer\Tests\Core\Autoloader\A',
                'PHP_CodeSniffer\Tests\Core\Autoloader\C',
                'PHP_CodeSniffer\Tests\Core\Autoloader\Sub\C',
                'PHP_CodeSniffer\Tests\Core\Autoloader\B',
            ],
            'interfaces' => [],
            'traits'     => [],
        ];

        $className = \PHP_CodeSniffer\Autoload::determineLoadedClass($classesBeforeLoad, $classesAfterLoad);
        $this->assertEquals('PHP_CodeSniffer\Tests\Core\Autoloader\Sub\C', $className);

        $classesAfterLoad = [
            'classes'    => [
                'PHP_CodeSniffer\Tests\Core\Autoloader\Sub\C',
                'PHP_CodeSniffer\Tests\Core\Autoloader\A',
                'PHP_CodeSniffer\Tests\Core\Autoloader\C',
                'PHP_CodeSniffer\Tests\Core\Autoloader\B',
            ],
            'interfaces' => [],
            'traits'     => [],
        ];

        $className = \PHP_CodeSniffer\Autoload::determineLoadedClass($classesBeforeLoad, $classesAfterLoad);
        $this->assertEquals('PHP_CodeSniffer\Tests\Core\Autoloader\Sub\C', $className);

    }//end testUnordered()


}//end class
